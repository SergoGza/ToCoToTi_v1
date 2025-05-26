<template>
  <Head :title="'Conversación con ' + conversation.other_user.name" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center">
        <Link :href="route('messages.index')" class="mr-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
          </svg>
        </Link>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {{ conversation.title }}
        </h2>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <!-- Mensajes -->
            <div class="h-96 overflow-y-auto mb-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg" ref="messagesContainer">
              <div v-if="messagesList.length" class="space-y-4">
                <div v-for="message in messagesList" :key="message.id"
                     :class="[
                      'max-w-3/4 rounded-lg p-3',
                      message.user_id === $page.props.auth.user.id
                          ? 'ml-auto bg-blue-100 dark:bg-blue-900 text-blue-900 dark:text-blue-100'
                          : 'mr-auto bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100'
                    ]">
                  <div class="flex items-center mb-1">
                    <span class="font-semibold text-sm">
                      {{ message.user_id === $page.props.auth.user.id ? 'Tú' : (message.user ? message.user.name : 'Usuario') }}
                    </span>
                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">
                      {{ formatTime(message.created_at) }}
                    </span>
                  </div>
                  <p>{{ extractMessageContent(message.content) }}</p>
                </div>
              </div>
              <div v-else class="flex items-center justify-center h-full">
                <p class="text-gray-500 dark:text-gray-400">
                  Todavía no hay mensajes en esta conversación. ¡Envía el primero!
                </p>
              </div>
            </div>

            <!-- Formulario para enviar mensajes -->
            <form @submit.prevent="sendMessage" class="mt-4">
              <div class="flex items-end">
                <textarea
                    v-model="form.content"
                    class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    rows="2"
                    placeholder="Escribe tu mensaje..."
                    @keydown.enter.exact.prevent="sendMessage"
                ></textarea>
                <button
                    type="submit"
                    class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
                    :disabled="form.processing || !form.content.trim()"
                >
                  Enviar
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePage } from '@inertiajs/vue3';

// Props
const props = defineProps({
  conversation: Object,
  messages: Array
});

// Refs
const messagesContainer = ref(null);
const messagesList = ref([...props.messages]);

// Form
const form = useForm({
  content: ''
});

const extractMessageContent = (content) => {
  if (!content) return '';

  // Si el contenido ya es un string normal
  if (typeof content === 'string') {
    // Intentar parsear como JSON por si es una cadena serializada
    try {
      const parsed = JSON.parse(content);
      // Si es un objeto con propiedad 'content', extraer solo ese valor
      if (parsed && typeof parsed === 'object' && 'content' in parsed) {
        return parsed.content;
      }
    } catch (e) {
      // No es JSON, es solo texto normal
      return content;
    }
    return content;
  }

  // Si el contenido es un objeto con propiedad 'content'
  if (typeof content === 'object' && content !== null && 'content' in content) {
    return content.content;
  }

  // Último recurso: convertir a string
  return String(content);
};

// Scroll to bottom of messages
const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
  });
};

// Format time for messages
const formatTime = (dateString) => {
  if (!dateString) return '';

  const date = new Date(dateString);
  return date.toLocaleString('es-ES', {
    hour: '2-digit',
    minute: '2-digit',
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
};

// Send a message
const page = usePage();
const sendMessage = () => {
    if (!form.content.trim()) return;

    // Añadir el mensaje local inmediatamente para UI optimista
    messagesList.value.push({
        id: 'temp-' + Date.now(),
        content: form.content,
        user_id: page.props.auth.user.id,
        created_at: new Date().toISOString(),
        user: {
            name: page.props.auth.user.name
        }
    });

    // Hacer scroll
    scrollToBottom();

    // Guardar una referencia al contenido
    const originalContent = form.content;

    // Limpiar el formulario visualmente
    form.content = '';

    // Iniciar la petición usando axios en lugar del objeto form
    // Esto evita los comportamientos automáticos de restablecimiento de Inertia
    axios.post(route('messages.send', props.conversation.id), {
        content: originalContent
    }, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-No-Loading': 'true'
        }
    })
        .catch(error => {
            console.error('Error al enviar mensaje:', error);
            window.showToast('Error al enviar el mensaje', 'error');
        });
};

// Echo setup for real-time messages
onMounted(() => {
  scrollToBottom();

  // Configuración de Echo con verificación explícita de la conversación
  if (window.Echo && window.userId) {
    try {
      window.Echo.private(`user.${window.userId}`)
          .listen('NewMessageEvent', (event) => {
            // Extracción del mensaje con verificación de estructura
            const messageData = event.message || event;

            // Verificación explícita de la conversación
            if (messageData.conversation_id == props.conversation.id) {
              const newMessage = {
                id: messageData.id || Date.now(),
                conversation_id: messageData.conversation_id,
                user_id: messageData.user_id,
                content: messageData.content,
                created_at: messageData.created_at || new Date().toISOString(),
                user: {
                  id: messageData.user_id,
                  name: (messageData.user?.name || messageData.user_name || 'Usuario')
                }
              };

              // Añadir mensaje y hacer scroll
              messagesList.value.push(newMessage);
              scrollToBottom();
            }
          });
    } catch (error) {
      console.error('Error al configurar Echo:', error);
    }
  }
});

onUnmounted(() => {
  // Cleanup Echo
  if (window.Echo && window.userId) {
    try {
      window.Echo.leave(`private-user.${window.userId}`);
    } catch (error) {
      console.error('Error al desuscribirse del canal:', error);
    }
  }
});

// Watch for changes in props.messages
watch(() => props.messages, (newMessages) => {
  messagesList.value = [...newMessages];
  scrollToBottom();
}, {deep: true});
</script>
