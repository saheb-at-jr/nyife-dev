<!-- <script setup>
import { computed, ref, watch } from 'vue';
import { router, usePage } from "@inertiajs/vue3";
import Modal from '@/Components/Modal.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps(['type', 'modelValue']);
const emit = defineEmits(['update:modelValue']);
const flashResponse = computed(() => usePage().props.flash.status);

const modalLabel = props.type === 'contact' ? trans('Import contacts') : trans('Import contact groups');
const isOpenModal = ref(props.modelValue);
const fileName = ref(null);
const progressStatus = ref(null);
const showFailedTable = ref(false);

watch(() => props.modelValue, (newValue) => {
    isOpenModal.value = newValue;
});

const handleFileUpload = (event) => {
    event.preventDefault();
    const file = event.target.files[0];
    uploadFile(file);
}

const handleDrop = (event) => {
    event.preventDefault();
    const file = event.dataTransfer.files[0];
    uploadFile(file);
};

function uploadFile(file) {
    if (!['text/csv', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'].includes(file.type)) {
        alert(trans('please select a CSV or XLSX file'));
        return;
    }

    fileName.value = file.name;

    const formData = new FormData();
    formData.append('file', file);

    // Use Inertia router to send the POST request
    router.post(props.type === 'contact' ? '/contacts/import' : '/contact-groups/import', formData, {
        forceFormData: true,
        onProgress: event => {
            progressStatus.value = 'pending';
        },
        onSuccess: () => {
            progressStatus.value = 'complete';
        },
        onError: (errors) => {
            progressStatus.value = null;
        }
    });
}

function closeModal() {
    isOpenModal.value = false;
    emit('update:modelValue', false);
    setTimeout(() => {
        progressStatus.value = null;
    }, 500);
}

function toggleFailedTable() {
    showFailedTable.value = !showFailedTable.value;
}
</script>
<template>
    <Modal :label="modalLabel" :isOpen="isOpenModal">
        <div v-if="type === 'contact'" class="text-sm text-slate-600 mt-4">{{ $t(`Upload a csv/xlsx to import your
            contact data.For the phone field ensure that you start with the contact\'s country code.`) }}</div>
        <div v-else class="text-sm text-slate-600">{{ $t('Upload a csv/xlsx to import your contact groups data.') }}
        </div>
        <div class="text-sm text-slate-600 underline flex mt-4 mb-6">
            <a :href="type === 'contact' ? '/contacts/template' : '/contact-groups.xlsx'">{{ $t(`Click here to download
                sample template`) }}</a>
        </div>
        <div class="max-w-md w-full gap-y-8">
            <div class="space-y-6">
                <div v-if="progressStatus == null || progressStatus == 'complete'" @dragover.prevent @drop="handleDrop"
                    class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                    <input type="file" class="sr-only" accept=".csv,.xlsx" ref="fileInput" id="file-upload"
                        @change="handleFileUpload($event)" />
                    <div class="text-center">
                        <div>
                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24">
                                <g fill="none">
                                    <path fill="currentColor"
                                        d="m15.393 4.054l-.502.557l.502-.557Zm3.959 3.563l-.502.557l.502-.557Zm2.302 2.537l-.685.305l.685-.305ZM3.172 20.828l.53-.53l-.53.53Zm17.656 0l-.53-.53l.53.53ZM14 21.25h-4v1.5h4v-1.5ZM2.75 14v-4h-1.5v4h2.5Zm18.5-.437V14h2.5v-.437h-1.5ZM14.891 4.61l3.959 3.563l1.003-1.115l-3.958-3.563l-1.004 1.115Zm7.859 8.952c0-1.689.015-2.758-.41-3.714l-1.371.61c.266.598.281 1.283.281 3.104h2.5Zm-3.9-5.389c1.353 1.218 1.853 1.688 2.119 2.285l1.37-.61c-.426-.957-1.23-1.66-2.486-2.79L18.85 8.174ZM10.03 2.75c1.582 0 2.179.012 2.71.216l.538-1.4c-.852-.328-1.78-.316-3.248-.316v1.5Zm5.865.746c-1.086-.977-1.765-1.604-2.617-1.93l-.537 1.4c.532.204.98.592 2.15 1.645l1.004-1.115ZM10 21.25c-1.907 0-3.261-.002-4.29-.14c-1.005-.135-1.585-.389-2.008-.812l-1.06 1.06c.748.75 1.697 1.081 2.869 1.239c1.15.155 2.625.153 4.489.153v-1.5ZM1.25 14c0 1.864-.002 3.338.153 4.489c.158 1.172.49 2.121 1.238 2.87l1.06-1.06c-.422-.424-.676-1.004-.811-2.01c-.138-1.027-.14-2.382-.14-4.289h-1.5ZM14 22.75c1.864 0 3.338.002 4.489-.153c1.172-.158 2.121-.49 2.87-1.238l-1.06-1.06c-.424.422-1.004.676-2.01.811c-1.027.138-2.382.14-4.289.14v1.5ZM21.25 14c0 1.907-.002 3.262-.14 4.29c-.135 1.005-.389 1.585-.812 2.008l1.06 1.06c.75-.748 1.081-1.697 1.239-2.869c.155-1.15.153-2.625.153-4.489h-1.5Zm-18.5-4c0-1.907.002-3.261.14-4.29c.135-1.005.389-1.585.812-2.008l-1.06-1.06c-.75.748-1.081 1.697-1.239 2.869C1.248 6.661 1.25 8.136 1.25 10h2.5Zm7.28-8.75c-1.875 0-3.356-.002-4.511.153c-1.177.158-2.129.49-2.878 1.238l1.06 1.06c.424-.422 1.005-.676 2.017-.811c1.033-.138 2.395-.14 4.312-.14v-1.5Z" />
                                    <path stroke="currentColor" stroke-width="1.5"
                                        d="M13 2.5V5c0 2.357 0 3.536.732 4.268C14.464 10 15.643 10 18 10h4" />
                                </g>
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="file-upload"
                                    class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>{{ $t('Click to upload a file') }}</span>
                                </label>
                                <p class="pl-1">{{ $t('Or drag and drop') }}</p>
                            </div>
                            <p class="text-xs text-gray-500">{{ $t('CSV/XLSX files only') }}</p>
                        </div>
                    </div>
                </div>
                <div v-else
                    class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                    <div>
                        <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="50"
                            height="50" viewBox="0 0 24 24">
                            <path fill="none" stroke="black" stroke-dasharray="15" stroke-dashoffset="15"
                                stroke-linecap="round" stroke-width="2" d="M12 3C16.9706 3 21 7.02944 21 12">
                                <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.3s" values="15;0" />
                                <animateTransform attributeName="transform" dur="1.5s" repeatCount="indefinite"
                                    type="rotate" values="0 12 12;360 12 12" />
                            </path>
                        </svg>
                        <div class="text-center mb-2 text-sm text-gray-500">Upload In Progress</div>
                        <div class="rounded-md p-1 bg-slate-50 text-center text-sm">{{ fileName }}</div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="mt-2" v-if="progressStatus == 'complete'">
                        <div v-if="flashResponse.import_summary.successful_imports"
                            class="bg-green-50 px-2 py-2 flex rounded-md justify-between items-center mb-1">
                            <div class="mt-1 text-sm">
                                <div class="text-green-800 flex items-center gap-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 16 16">
                                        <g fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="1.5">
                                            <path
                                                d="M14.25 8.75c-.5 2.5-2.385 4.854-5.03 5.38A6.25 6.25 0 0 1 3.373 3.798C5.187 1.8 8.25 1.25 10.75 2.25" />
                                            <path d="m5.75 7.75l2.5 2.5l6-6.5" />
                                        </g>
                                    </svg>
                                    {{ flashResponse.import_summary.successful_imports + '/' +
                                        flashResponse.import_summary.total_imports }} {{ $t('rows added successfully!') }}
                                </div>
                            </div>
                        </div>
                        <div v-if="flashResponse.import_summary.duplicate_entries"
                            class="bg-red-50 px-2 py-2 flex rounded-md justify-between items-center mb-1">
                            <div class="mt-1 text-sm">
                                <div class="text-red-600 flex items-center gap-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="M15.71 8.29a1 1 0 0 0-1.42 0L12 10.59l-2.29-2.3a1 1 0 0 0-1.42 1.42l2.3 2.29l-2.3 2.29a1 1 0 0 0 0 1.42a1 1 0 0 0 1.42 0l2.29-2.3l2.29 2.3a1 1 0 0 0 1.42 0a1 1 0 0 0 0-1.42L13.41 12l2.3-2.29a1 1 0 0 0 0-1.42m3.36-3.36A10 10 0 1 0 4.93 19.07A10 10 0 1 0 19.07 4.93m-1.41 12.73A8 8 0 1 1 20 12a7.95 7.95 0 0 1-2.34 5.66" />
                                    </svg>
                                    {{ flashResponse.import_summary.duplicate_entries }} duplicates found
                                </div>
                            </div>
                        </div>
                        <div v-if="flashResponse.import_summary.invalid_format_entries"
                            class="bg-red-50 px-2 py-2 flex rounded-md justify-between items-center mb-1">
                            <div class="mt-1 text-sm">
                                <div class="text-red-600 flex items-center gap-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="M15.71 8.29a1 1 0 0 0-1.42 0L12 10.59l-2.29-2.3a1 1 0 0 0-1.42 1.42l2.3 2.29l-2.3 2.29a1 1 0 0 0 0 1.42a1 1 0 0 0 1.42 0l2.29-2.3l2.29 2.3a1 1 0 0 0 1.42 0a1 1 0 0 0 0-1.42L13.41 12l2.3-2.29a1 1 0 0 0 0-1.42m3.36-3.36A10 10 0 1 0 4.93 19.07A10 10 0 1 0 19.07 4.93m-1.41 12.73A8 8 0 1 1 20 12a7.95 7.95 0 0 1-2.34 5.66" />
                                    </svg>
                                    {{ flashResponse.import_summary.invalid_format_entries }} formatting issues found
                                </div>
                            </div>
                        </div>
                        <div v-if="flashResponse.import_summary.failed_limit_entries"
                            class="bg-red-50 px-2 py-2 flex rounded-md justify-between items-center mb-1">
                            <div class="mt-1 text-sm">
                                <div class="text-red-600 flex items-center gap-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="M15.71 8.29a1 1 0 0 0-1.42 0L12 10.59l-2.29-2.3a1 1 0 0 0-1.42 1.42l2.3 2.29l-2.3 2.29a1 1 0 0 0 0 1.42a1 1 0 0 0 1.42 0l2.29-2.3l2.29 2.3a1 1 0 0 0 1.42 0a1 1 0 0 0 0-1.42L13.41 12l2.3-2.29a1 1 0 0 0 0-1.42m3.36-3.36A10 10 0 1 0 4.93 19.07A10 10 0 1 0 19.07 4.93m-1.41 12.73A8 8 0 1 1 20 12a7.95 7.95 0 0 1-2.34 5.66" />
                                    </svg>
                                    {{ flashResponse.import_summary.failed_limit_entries }} failed due to contact limit
                                </div>
                            </div>
                        </div>
                        <div
                            v-if="flashResponse.import_summary.failed_rows_details && flashResponse.import_summary.failed_rows_details.length > 0">
                            <div class="mt-2">
                                <div
                                    class="bg-slate-50 text-sm grid grid-cols-2 p-1 py-2 rounded-md border-b-2 border-white">
                                    <div class="col-1 pl-2">{{ $t('Row') }}</div>
                                    <div class="col-1 flex justify-between items-center">
                                        <span>{{ $t('Error') }}</span>
                                        <button @click="toggleFailedTable"
                                            class="bg-primary text-white text-[11px] rounded px-2">{{ showFailedTable ?
                                                $t('Hide') : $t('Show') }}</button>
                                    </div>
                                </div>
                                <div v-if="showFailedTable"
                                    class="bg-red-50 text-[12px] grid grid-cols-2 border-white border-r border-l border-b p-1 rounded-md"
                                    v-for="(item, index) in flashResponse.import_summary.failed_rows_details"
                                    :key="index">
                                    <div class="col-1 pl-2">{{ item.row }}</div>
                                    <div class="col-1">{{ item.error }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="progressStatus == null || progressStatus == 'complete'" class="mt-5">
            <div class="flex justify-center mt-2 w-full">
                <button type="button" @click="closeModal()"
                    class="w-full inline-flex justify-center rounded-md border border-transparent bg-primary px-4 py-2 text-sm text-white hover:text-slate-500 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">{{
                        $t('Close') }}</button>
            </div>
        </div>
    </Modal>
</template> -->


<!-- ========================================== NEW UI CODE ==================================== -->

<script setup>
import { computed, ref, watch } from 'vue';
import { router, usePage } from "@inertiajs/vue3";
import Modal from '@/Components/Modal.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps(['type', 'modelValue']);
const emit = defineEmits(['update:modelValue']);
const flashResponse = computed(() => usePage().props.flash.status);

const modalLabel = props.type === 'contact' ? trans('Import contacts') : trans('Import contact groups');
const isOpenModal = ref(props.modelValue);
const fileName = ref(null);
const progressStatus = ref(null);
const showFailedTable = ref(false);

watch(() => props.modelValue, (newValue) => {
    isOpenModal.value = newValue;
});

const handleFileUpload = (event) => {
    event.preventDefault();
    const file = event.target.files[0];
    uploadFile(file);
}

const handleDrop = (event) => {
    event.preventDefault();
    const file = event.dataTransfer.files[0];
    uploadFile(file);
};

function uploadFile(file) {
    if (!['text/csv', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'].includes(file.type)) {
        alert(trans('please select a CSV or XLSX file'));
        return;
    }

    fileName.value = file.name;

    const formData = new FormData();
    formData.append('file', file);

    router.post(props.type === 'contact' ? '/contacts/import' : '/contact-groups/import', formData, {
        forceFormData: true,
        onProgress: event => {
            progressStatus.value = 'pending';
        },
        onSuccess: () => {
            progressStatus.value = 'complete';
        },
        onError: (errors) => {
            progressStatus.value = null;
        }
    });
}

function closeModal() {
    isOpenModal.value = false;
    emit('update:modelValue', false);
    setTimeout(() => {
        progressStatus.value = null;
    }, 500);
}

function toggleFailedTable() {
    showFailedTable.value = !showFailedTable.value;
}
</script>

<template>
    <Modal :label="modalLabel" :isOpen="isOpenModal">
        <div class="mt-4">
            <!-- Instructions -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
                <div class="flex gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        class="text-blue-600 flex-shrink-0 mt-0.5">
                        <path fill="currentColor"
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                    </svg>
                    <div class="text-sm text-blue-900">
                        <p v-if="type === 'contact'" class="font-medium mb-1">{{ $t('Important Guidelines') }}</p>
                        <p v-if="type === 'contact'">{{ $t(`Upload a csv/xlsx to import your contact data. For the phone
                            field ensure that you start with the contact\'s country code.`) }}</p>
                        <p v-else>{{ $t('Upload a csv/xlsx to import your contact groups data.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Download Template Link -->
            <a :href="type === 'contact' ? '/contacts/template' : '/contact-groups.xlsx'"
                class="inline-flex items-center gap-2 text-sm text-[#ff5100] hover:text-[#e64900] font-medium mb-6 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                    <path fill="currentColor"
                        d="M13 16v-5.5l2.6 2.6L17 11l-5-5l-5 5l1.4 1.45l2.6-2.6V16h2Zm-7 4q-.825 0-1.413-.588T4 18v-3h2v3h12v-3h2v3q0 .825-.588 1.413T18 20H6Z" />
                </svg>
                {{ $t('Click here to download sample template') }}
            </a>

            <!-- Upload Area -->
            <div v-if="progressStatus == null || progressStatus == 'complete'" @dragover.prevent @drop="handleDrop"
                class="relative border-2 border-dashed border-gray-300 hover:border-[#ff5100] rounded-2xl p-8 text-center transition-all group cursor-pointer bg-gradient-to-br from-gray-50 to-orange-50/20">
                <input type="file" class="sr-only" accept=".csv,.xlsx" id="file-upload"
                    @change="handleFileUpload($event)" />

                <div class="flex flex-col items-center gap-4">
                    <div
                        class="w-16 h-16 rounded-full bg-[#ff5100]/10 flex items-center justify-center group-hover:bg-[#ff5100]/20 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                            class="text-[#ff5100]">
                            <path fill="currentColor"
                                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm4 18H6V4h7v5h5v11zm-6-1h2v-4h3l-4-4l-4 4h3v4z" />
                        </svg>
                    </div>

                    <div>
                        <label for="file-upload" class="cursor-pointer">
                            <span class="text-[#ff5100] hover:text-[#e64900] font-semibold">{{ $t(`Click to upload a
                                file`) }}</span>
                            <span class="text-gray-600"> {{ $t(' or drag and drop') }}</span>
                        </label>
                        <p class="text-sm text-gray-500 mt-2">{{ $t('CSV/XLSX files only') }}</p>
                    </div>
                </div>
            </div>

            <!-- Upload Progress -->
            <div v-else class="border-2 border-gray-200 rounded-2xl p-8 text-center bg-gray-50">
                <div class="flex flex-col items-center gap-4">
                    <div class="relative">
                        <svg class="w-16 h-16 text-[#ff5100]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-dasharray="15" stroke-dashoffset="15"
                                stroke-linecap="round" stroke-width="2" d="M12 3C16.9706 3 21 7.02944 21 12">
                                <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.3s" values="15;0" />
                                <animateTransform attributeName="transform" dur="1.5s" repeatCount="indefinite"
                                    type="rotate" values="0 12 12;360 12 12" />
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 mb-2">{{ $t('Upload In Progress') }}</p>
                        <div
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-lg text-sm text-gray-700 border border-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z" />
                            </svg>
                            {{ fileName }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Summary -->
            <div v-if="progressStatus == 'complete'" class="mt-6 space-y-3">
                <!-- Success -->
                <div v-if="flashResponse.import_summary.successful_imports"
                    class="flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-xl">
                    <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 16 16"
                            class="text-green-600">
                            <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="1.5">
                                <path
                                    d="M14.25 8.75c-.5 2.5-2.385 4.854-5.03 5.38A6.25 6.25 0 0 1 3.373 3.798C5.187 1.8 8.25 1.25 10.75 2.25" />
                                <path d="m5.75 7.75l2.5 2.5l6-6.5" />
                            </g>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-green-900">{{ $t('Import Successful') }}</p>
                        <p class="text-sm text-green-700 mt-1">
                            {{ flashResponse.import_summary.successful_imports }}/{{
                                flashResponse.import_summary.total_imports }} {{ $t('rows added successfully!') }}
                        </p>
                    </div>
                </div>

                <!-- Duplicates -->
                <div v-if="flashResponse.import_summary.duplicate_entries"
                    class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            class="text-amber-600">
                            <path fill="currentColor"
                                d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-amber-900">{{ $t('Duplicates Found') }}</p>
                        <p class="text-sm text-amber-700 mt-1">{{ flashResponse.import_summary.duplicate_entries }} {{
                            $t('duplicate entries') }}</p>
                    </div>
                </div>

                <!-- Format Issues -->
                <div v-if="flashResponse.import_summary.invalid_format_entries"
                    class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            class="text-red-600">
                            <path fill="currentColor"
                                d="M15.71 8.29a1 1 0 0 0-1.42 0L12 10.59l-2.29-2.3a1 1 0 0 0-1.42 1.42l2.3 2.29l-2.3 2.29a1 1 0 0 0 0 1.42a1 1 0 0 0 1.42 0l2.29-2.3l2.29 2.3a1 1 0 0 0 1.42 0a1 1 0 0 0 0-1.42L13.41 12l2.3-2.29a1 1 0 0 0 0-1.42m3.36-3.36A10 10 0 1 0 4.93 19.07A10 10 0 1 0 19.07 4.93m-1.41 12.73A8 8 0 1 1 20 12a7.95 7.95 0 0 1-2.34 5.66" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-red-900">{{ $t('Formatting Issues') }}</p>
                        <p class="text-sm text-red-700 mt-1">{{ flashResponse.import_summary.invalid_format_entries }}
                            {{ $t('formatting issues found') }}</p>
                    </div>
                </div>

                <!-- Limit Errors -->
                <div v-if="flashResponse.import_summary.failed_limit_entries"
                    class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            class="text-red-600">
                            <path fill="currentColor"
                                d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-red-900">{{ $t('Limit Exceeded') }}</p>
                        <p class="text-sm text-red-700 mt-1">{{ flashResponse.import_summary.failed_limit_entries }} {{
                            $t('failed due to contact limit') }}</p>
                    </div>
                </div>

                <!-- Failed Rows Details -->
                <div v-if="flashResponse.import_summary.failed_rows_details && flashResponse.import_summary.failed_rows_details.length > 0"
                    class="border border-gray-200 rounded-xl overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-b border-gray-200">
                        <span class="text-sm font-semibold text-gray-900">{{ $t('Error Details') }}</span>
                        <button @click="toggleFailedTable"
                            class="px-3 py-1.5 bg-[#ff5100] hover:bg-[#e64900] text-white text-xs font-medium rounded-lg transition-colors">
                            {{ showFailedTable ? $t('Hide') : $t('Show') }}
                        </button>
                    </div>

                    <div v-if="showFailedTable" class="max-h-64 overflow-y-auto">
                        <div v-for="(item, index) in flashResponse.import_summary.failed_rows_details" :key="index"
                            class="grid grid-cols-2 gap-4 px-4 py-3 text-sm border-b border-gray-100 last:border-b-0 hover:bg-gray-50">
                            <div class="font-medium text-gray-900">{{ $t('Row') }} {{ item.row }}</div>
                            <div class="text-red-600">{{ item.error }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Button -->
        <div v-if="progressStatus == null || progressStatus == 'complete'" class="mt-6">
            <button type="button" @click="closeModal()"
                class="w-full px-6 py-3 bg-[#ff5100] hover:bg-[#e64900] text-white font-medium rounded-xl transition-all shadow-md hover:shadow-lg">
                {{ $t('Close') }}
            </button>
        </div>
    </Modal>
</template>