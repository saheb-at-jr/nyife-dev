<!-- <script setup>
    import { ref, watchEffect } from 'vue';
    import { router, useForm } from '@inertiajs/vue3';
    import FormModal from '@/Components/FormModal.vue';
    import { trans } from 'laravel-vue-i18n';
    
    const props = defineProps(['group']);
    const group = ref(props.group);

    watchEffect(() => {
        group.value = props.group;
    });

    const isOpenFormModal = ref(false);
    const form = ref({
        name: group.value.name,
    });

    const formInputs = [
        {
            inputType: 'FormInput',
            name: 'name',
            label: trans('name'),
            type: 'text',
            className: 'sm:col-span-6',
        },
    ];

    const form2 = useForm({'test': null});

    const deleteRow = async() => {
        router.visit('/contact-groups', {
            method: 'delete',
            data: { 'uuids': [ group.value.uuid ]},
            preserveState: true
        })
    }

    const openModal = () => {
        isOpenFormModal.value = true;
    }
</script>
<template>
    <div>
        <div class="pt-20">
            <div class="flex justify-center space-x-8 items-center pb-6 pr-20 border-gray-300 border-b">
                <div>
                    <div class="rounded-full p-1 bg-white">
                        <div class="rounded-full text-3xl flex justify-center items-center h-24 w-24 capitalize">{{ group.name.substring(0, 1) }}</div>
                    </div>
                </div>
                <div>
                    <h1 class="text-3xl">{{ group.name }}</h1>
                    <div class="flex space-x-3 mt-4">
                        <button class="bg-gray-200 py-2 px-4 h-9 rounded-md flex items-center" @click="openModal('edit')">
                            <span class="text-[14px]">{{ $t('Edit') }}</span>
                        </button>
                        <button @click="deleteRow()" class="bg-gray-200 py-2 px-4 h-9 rounded-md flex items-center">
                            <span class="text-[14px]">{{ $t('Delete') }}</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="pr-20 border-gray-300 border-b py-4">
                <div class="grid grid-cols-2 space-x-8 text-[14px]">
                    <div class="text-right text-slate-500 pb-2">
                        <span>{{ $t('Group name') }}</span>
                    </div>
                    <div>
                        <span>{{ group.name }}</span>
                    </div>
                    <div class="text-right text-slate-500 pb-2">
                        <span>{{ $t('Total contacts') }}</span>
                    </div>
                    <div>
                        <span class="p-1 bg-gray-50 text-xs rounded-lg text-gray-600">{{ group.contact_count }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <FormModal 
        v-model="isOpenFormModal" 
        :label="$t('Edit group')" 
        :url="'/contact-groups/' + group?.uuid" 
        :form="form"
        :formInputs="formInputs"
    />
</template> -->


<!-- ========================================== NEW UI CODE ==================================== -->

<script setup>
import { ref, watchEffect } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import FormModal from '@/Components/FormModal.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps(['group']);
const group = ref(props.group);

watchEffect(() => {
    group.value = props.group;
});

const isOpenFormModal = ref(false);
const form = ref({
    name: group.value.name,
});

const formInputs = [
    {
        inputType: 'FormInput',
        name: 'name',
        label: trans('name'),
        type: 'text',
        className: 'sm:col-span-6',
    },
];

const form2 = useForm({ 'test': null });

const deleteRow = async () => {
    router.visit('/contact-groups', {
        method: 'delete',
        data: { 'uuids': [group.value.uuid] },
        preserveState: true
    })
}

const openModal = () => {
    isOpenFormModal.value = true;
}
</script>

<template>
    <div class="h-screen overflow-y-auto bg-gradient-to-br from-gray-50 to-orange-50/30">
        <div class="max-w-4xl mx-auto p-8">

            <!-- Hero Section -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-6">
                <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        <div
                            class="w-32 h-32 rounded-full bg-gradient-to-br from-[#ff5100] to-orange-400 flex items-center justify-center">
                            <span class="text-5xl font-bold text-white capitalize">{{ group.name.substring(0, 1)
                                }}</span>
                        </div>
                    </div>

                    <!-- Info & Actions -->
                    <div class="flex-1 text-center md:text-left">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ group.name }}</h1>
                        <p class="text-gray-500 mb-6 flex items-center justify-center md:justify-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5S5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05c1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                            </svg>
                            <span>{{ group.contact_count }} {{ $t('contacts') }}</span>
                        </p>

                        <div class="flex flex-wrap gap-3 justify-center md:justify-start">
                            <button @click="openModal()"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#ff5100] hover:bg-[#e64900] text-white rounded-xl font-medium transition-all shadow-md hover:shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a.9959.9959 0 0 0-1.41 0l-1.83 1.83l3.75 3.75l1.83-1.83z" />
                                </svg>
                                {{ $t('Edit') }}
                            </button>

                            <button @click="deleteRow()"
                                class="inline-flex items-center gap-2 px-6 py-2.5 border-2 border-red-200 hover:border-red-500 hover:bg-red-50 rounded-xl font-medium text-red-600 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M7 21q-.825 0-1.413-.588T5 19V6H4V4h5V3h6v1h5v2h-1v13q0 .825-.588 1.413T17 21H7ZM17 6H7v13h10V6ZM9 17h2V8H9v9Zm4 0h2V8h-2v9ZM7 6v13V6Z" />
                                </svg>
                                {{ $t('Delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Group Details -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-[#ff5100]/10 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            class="text-[#ff5100]">
                            <path fill="currentColor"
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3s-3-1.34-3-3s1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22c.03-1.99 4-3.08 6-3.08c1.99 0 5.97 1.09 6 3.08c-1.29 1.94-3.5 3.22-6 3.22z" />
                        </svg>
                    </div>
                    {{ $t('Group Information') }}
                </h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-500">{{ $t('Group name') }}</label>
                        <p class="text-gray-900 font-medium">{{ group.name }}</p>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-500">{{ $t('Total contacts') }}</label>
                        <p class="text-gray-900">
                            <span
                                class="inline-flex items-center gap-1 px-3 py-1 bg-[#ff5100]/10 text-[#ff5100] rounded-lg text-sm font-medium">
                                {{ group.contact_count }} {{ $t('contacts') }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <FormModal v-model="isOpenFormModal" :label="$t('Edit group')" :url="'/contact-groups/' + group?.uuid" :form="form"
        :formInputs="formInputs" />
</template>