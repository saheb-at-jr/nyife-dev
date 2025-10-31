<!-- <script setup>
const props = defineProps({
    modelValue: [String, Number],
    label: String,
    name: String,
    placeholder: String,
    type: String,
    min: Number,
    className: String,
    labelClass: String,
    required: Boolean,
    error: String,
    disabled: Boolean
})

const emit = defineEmits(['update:modelValue']);
const updateValue = (event) => {
    emit('update:modelValue', event.target.value);
};
</script>
<template>
    <div :class="className">
        <label for="name" class="block text-sm leading-6 text-gray-900" :class="labelClass">{{ label ?? name }}</label>
        <div>
            <input
                class="block w-full rounded-md border-0 py-1.5 px-4 text-gray-900 shadow-sm outline-none ring-1 ring-inset placeholder:text-gray-400 sm:text-sm sm:leading-6"
                :class="error ? 'ring-[#b91c1c]' : 'ring-gray-300'" :type="type" :value="props.modelValue"
                @input="updateValue" :step="'any'" :min="min" :placeholder="placeholder" :disabled="disabled"
                :required="required" />
        </div>
        <div v-if="error" class="form-error text-[#b91c1c] text-xs">{{ error }}</div>
    </div>
</template> -->

<!-- ================================ NEW CODE ================================ -->

<script setup>
import { defineProps, defineEmits, ref, computed, watch, toRefs } from 'vue';

const props = defineProps({
    modelValue: [String, Number],
    label: String,
    name: { type: String, default: '' },
    placeholder: String,
    type: { type: String, default: 'text' },
    min: Number,
    className: { type: String, default: '' },
    labelClass: { type: String, default: '' },
    required: Boolean,
    error: String,
    disabled: Boolean
});

const emit = defineEmits(['update:modelValue']);

const {
    modelValue,
    label,
    name,
    placeholder,
    type,
    min,
    className,
    labelClass,
    required,
    error,
    disabled
} = toRefs(props);

// local value sync
const localValue = ref(modelValue.value ?? '');
watch(modelValue, (v) => (localValue.value = v ?? ''));

// update emitter
const updateValue = (e) => {
    const v = e.target.value;
    localValue.value = v;
    emit('update:modelValue', v);
};

const isPassword = computed(() => type.value === 'password');
const showPassword = ref(false);
const inputType = computed(() => (isPassword.value ? (showPassword.value ? 'text' : 'password') : type.value || 'text'));

// generate safe id
const id = computed(() => name.value || `input-${Math.random().toString(36).slice(2, 8)}`);
</script>

<template>
    <div :class="['flex flex-col', className]">
        <label :for="id" class="mb-2 text-sm font-medium text-slate-700 dark:text-slate-200" :class="labelClass">
            {{ label ?? name }}
            <span v-if="required" class="ml-1 text-red-500" aria-hidden="true">*</span>
        </label>

        <div class="relative">
            <input :id="id" :name="name" :type="inputType" v-model="localValue" @input="updateValue"
                :placeholder="placeholder" :disabled="disabled" :required="required" :min="min" :aria-invalid="!!error"
                :aria-describedby="error ? `${id}-error` : null"
                class="block w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 py-2.5 px-4 text-sm text-slate-900 dark:text-slate-100 placeholder:text-slate-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-primary/30 transition" />

            <!-- password toggle -->
            <button v-if="isPassword" type="button" @click="showPassword = !showPassword" :aria-pressed="showPassword"
                class="absolute right-2 top-1/2 -translate-y-1/2 text-xs px-2 py-1 rounded-md hover:bg-slate-100 dark:hover:bg-slate-700/40 focus:outline-none">
                <span v-if="showPassword">Hide</span>
                <span v-else>Show</span>
            </button>
        </div>

        <p v-if="error" :id="`${id}-error`" class="mt-1 text-xs text-[#b91c1c]">
            {{ error }}
        </p>
    </div>
</template>

<style scoped>
/* subtle focus shadow (shadcn feel) */
input:focus {
    box-shadow: 0 8px 30px rgba(99, 102, 241, 0.06);
}
</style>
