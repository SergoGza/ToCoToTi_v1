<!-- resources/js/Components/InputField.vue -->
<template>
    <div>
        <InputLabel :for="id" :value="label" />
        <TextInput
            :id="id"
            :type="type"
            class="mt-1 block w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="placeholder"
            :required="required"
            :autofocus="autofocus"
        />
        <InputError class="mt-2" :message="error" />
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

defineEmits(['update:modelValue']);

defineProps({
    id: {
        type: String,
        required: true
    },
    label: {
        type: String,
        required: true
    },
    modelValue: {
        type: [String, Number],
        default: ''
    },
    type: {
        type: String,
        default: 'text'
    },
    placeholder: {
        type: String,
        default: ''
    },
    required: {
        type: Boolean,
        default: false
    },
    autofocus: {
        type: Boolean,
        default: false
    },
    error: {
        type: String,
        default: ''
    }
});
</script>
