<!-- <script setup>
import axios from "axios";
import FormInput from '@/Components/FormInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import WhatsappTemplate from '@/Components/WhatsappTemplate.vue';
import { ref, computed, onMounted } from 'vue';
import { Link, useForm } from "@inertiajs/vue3";
import 'vue3-toastify/dist/index.css';
import { trans } from 'laravel-vue-i18n';
import Backup from './CrouselSend.vue';
import FlowTemplate from './FlowTemplate.vue';

const props = defineProps({
    templates: Object,
    contactGroups: Object,
    settings: Array,
    contact: {
        type: String,
        default: null
    },
    displayTitle: {
        type: Boolean,
        default: false
    },
    displayCancelBtn: {
        type: Boolean,
        default: true
    },
    isCampaignFlow: {
        type: Boolean,
        default: true
    },
    sendText: {
        type: String,
        default: 'Save'
    }
});
const isLoading = ref(false);
const contactGroupOptions = ref([
    { value: 'all', label: trans('all contacts') },
]);
const templateOptions = ref([]);
const config = ref(props.settings?.metadata);
const settings = ref(config.value ? JSON.parse(config.value) : null);
const showModal = ref(true);
const userChoice = ref(null);
const sharedProps = computed(() => ({
    templates: props.templates,
    contactGroups: props.contactGroups,
    settings: props.settings,
    contact: props.contact,
    displayTitle: props.displayTitle,
    displayCancelBtn: props.displayCancelBtn,
    isCampaignFlow: props.isCampaignFlow,
    sendText: props.sendText,
    form,
    settingsParsed: settings.value,
    isLoading: isLoading.value,
    contactGroupOptions: contactGroupOptions.value,
    templateOptions: templateOptions.value,
    variableOptions: variableOptions.value,
    dynamicOptions: dynamicOptions.value,
}));

const chooseOption = (option) => {
    userChoice.value = option;
    showModal.value = false;
};
const variableOptions = ref([
    { value: 'static', label: trans('static') },
    { value: 'dynamic', label: trans('dynamic') }
]);

const dynamicOptions = ref([
    { value: 'first name', label: trans('contact first name') },
    { value: 'last name', label: trans('contact last name') },
    { value: 'name', label: trans('contact full name') },
    { value: 'phone', label: trans('contact phone') },
    { value: 'email', label: trans('contact email') },
]);

const form = useForm({
    name: null,
    template: null,
    contacts: null,
    time: null,
    skip_schedule: false,
    'header': {
        'format': null,
        'text': null,
        'parameters': []
    },
    'body': {
        'text': null,
        'parameters': []
    },
    'footer': {
        'text': null,
    },
    'buttons': [],
});

const loadTemplate = async () => {
    try {
        const response = await axios.get('/templates/' + form.template);
        if (response) {
            const metadata = JSON.parse(response.data.metadata);
            form.header.format = extractComponent(metadata, 'HEADER', 'format');

            form.header.text = extractComponent(metadata, 'HEADER', 'text');
            const headerExamples = extractComponent(metadata, 'HEADER', 'example');
            if (headerExamples) {
                if (form.header.format === 'TEXT') {
                    form.header.parameters = headerExamples.header_text.map(item => ({
                        type: 'text',
                        selection: 'static',
                        value: item,
                    }));
                } else if (form.header.format === 'IMAGE' || form.header.format === 'DOCUMENT' || form.header.format === 'VIDEO') {
                    form.header.parameters = headerExamples.header_handle.map(item => ({
                        type: form.header.format,
                        selection: 'default',
                        value: null,
                        url: item,
                    }));
                }
            } else {
                form.header.parameters = [];
            }

            form.body.text = extractComponent(metadata, 'BODY', 'text');
            const bodyExamples = extractComponent(metadata, 'BODY', 'example');
            if (bodyExamples) {
                form.body.parameters = bodyExamples.body_text[0].map(item => ({
                    type: 'text',
                    selection: 'static',
                    value: item,
                }));
            } else {
                form.body.parameters = [];
            }

            form.footer.text = extractComponent(metadata, 'FOOTER', 'text');

            const buttons = extractComponent(metadata, 'BUTTONS', 'buttons');
            if (buttons) {
                form.buttons = buttons.map(item => ({
                    type: item.type,
                    text: item.text,
                    value: item[item.type.toLowerCase()] ?? null,
                    parameters: (item.type === 'QUICK_REPLY')
                        ? [{ type: 'static', value: null }]
                        : (item.example
                            ? item.example.map(param => ({ type: 'static', value: param }))
                            : []
                        ),
                }));
            } else {
                form.buttons = [];
            }

        }
    } catch (error) {
    }
}

const handleFileUpload = (event) => {
    const fileSizeLimit = getFileSizeLimit(form.header.parameters[0].type);
    const file = event.target.files[0];

    if (file && file.size > fileSizeLimit) {
        // Handle file size exceeding the limit
        alert(trans('file size exceeds the limit. Max allowed size:') + ' ' + fileSizeLimit + 'b');
        // Clear the file input
        event.target.value = null;
    } else {
        const reader = new FileReader();

        reader.onload = (e) => {
            form.header.parameters[0].url = e.target.result;
        };

        form.header.parameters[0].selection = 'upload';
        form.header.parameters[0].value = file;

        // Start reading the file
        reader.readAsDataURL(file);
    }
}

const getFileAcceptAttribute = (fileType) => {
    switch (fileType) {
        case 'IMAGE':
            return '.png, .jpg';
        case 'DOCUMENT':
            return '.pdf, .txt, .ppt, .doc, .xls, .docx, .pptx, .xlsx';
        case 'VIDEO':
            return '.mp4';
        default:
            return '';
    }
}

const getFileSizeLimit = (fileType) => {
    switch (fileType) {
        case 'IMAGE':
            return 5 * 1024 * 1024; // 5MB
        case 'DOCUMENT':
            return 100 * 1024 * 1024; // 100MB
        case 'VIDEO':
            return 16 * 1024 * 1024; // 16MB
        default:
            return Infinity;
    }
}

const extractComponent = (data, type, customProperty) => {
    const component = data.components.find(
        (c) => c.type === type
    );

    return component ? component[customProperty] : null;
};

const transformOptions = (options) => {
    return options.map((option) => ({
        value: option.uuid,
        label: option.language ? option.name + ' [' + option.language + ']' : option.name,
    }));
};

const submitForm = () => {
    isLoading.value = true;
    form.post(props.isCampaignFlow ? '/campaigns' : '/chat/' + props.contact + '/send/template', {
        onFinish: () => {
            isLoading.value = false;
            if (!props.isCampaignFlow) {
                emit('viewTemplate', false);
            }
        },
    });
}

const emit = defineEmits(['viewTemplate']);

const viewTemplate = () => {
    emit('viewTemplate', false);
}

onMounted(() => {
    // Filter out templates starting with "carousel_" or "flow_"
    const filteredTemplates = props.templates.filter(template =>
        !template.name?.startsWith('crousel_') && !template.name?.startsWith('flow_')
    );

    templateOptions.value = transformOptions(filteredTemplates);

    contactGroupOptions.value = [
        ...contactGroupOptions.value,
        ...transformOptions(props.contactGroups)
    ];
});


</script>
<template>
    <div v-if="showModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-2xl shadow-2xl p-6 w-[60%] max-w-md text-center">
            <h2 class="text-xl font-semibold mb-6 text-gray-800">Choose an Option</h2>

            <div class="flex flex-col gap-4">
                <button @click="chooseOption('template')"
                    class="w-full text-white py-2 rounded-xl transition-all duration-200"
                    style="background-color: #ff5200;">
                    Template
                </button>

                <button @click="chooseOption('carousel')"
                    class="w-full text-white py-2 rounded-xl transition-all duration-200"
                    style="background-color: #ff5200;">
                    Carousel
                </button>

                <button @click="chooseOption('flow')"
                    class="w-full text-white py-2 rounded-xl transition-all duration-200"
                    style="background-color: #ff5200;">
                    Flow
                </button>
            </div>
        </div>
    </div>


    <component v-if="userChoice === 'flow'" :is="FlowTemplate" v-bind="sharedProps" />
    <component v-else :is="userChoice === 'carousel' ? Backup : 'div'" v-bind="sharedProps">

        <template v-if="userChoice === 'template'">
            <div class="pb-100px">
                <div :class="'md:flex px md:flex-grow-1'">
                    <div v-if="!settings?.whatsapp" class="md:w-[50%] p-4 md:p-8 overflow-y-auto h-[90vh]">
                        <div class="bg-slate-50 border border-primary shadow rounded-md p-4 py-8">
                            <div class="flex justify-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="72" height="72" viewBox="0 0 48 48">
                                    <path fill="black"
                                        d="M43.634 4.366a1.25 1.25 0 0 1 0 1.768l-4.913 4.913a9.253 9.253 0 0 1-.744 12.244l-3.343 3.343a1.25 1.25 0 0 1-1.768 0l-11.5-11.5a1.25 1.25 0 0 1 0-1.768l3.343-3.343a9.25 9.25 0 0 1 12.244-.743l4.913-4.914a1.25 1.25 0 0 1 1.768 0m-7.611 7.425a6.75 6.75 0 0 0-9.546 0l-2.46 2.459l9.733 9.732l2.46-2.459a6.75 6.75 0 0 0 0-9.546zM9.28 36.953l-4.914 4.913a1.25 1.25 0 0 0 1.768 1.768l4.913-4.913a9.253 9.253 0 0 0 12.244-.744l3.343-3.343a1.25 1.25 0 0 0 0-1.768L25.268 31.5l3.366-3.366a1.25 1.25 0 0 0-1.768-1.768L23.5 29.732L18.268 24.5l3.366-3.366a1.25 1.25 0 0 0-1.768-1.768L16.5 22.732l-1.366-1.366a1.25 1.25 0 0 0-1.768 0l-3.343 3.343a9.25 9.25 0 0 0-.743 12.244m2.51-10.476l2.46-2.46l9.732 9.733l-2.459 2.46a6.75 6.75 0 0 1-9.546 0l-.186-.187a6.75 6.75 0 0 1 0-9.546" />
                                </svg>
                            </div>
                            <h3 class="text-center text-lg font-medium mb-4">{{ $t('Connect your whatsapp account') }}
                            </h3>
                            <h4 class="text-center mb-4">{{ $t(`You need to connect your WhatsApp account first before
                                you can send out campaigns.`) }}</h4>
                            <div class="flex justify-center">
                                <Link href="/settings/whatsapp"
                                    class="rounded-md px-3 py-2 text-sm hover:shadow-md text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 bg-primary"
                                    :disabled="isLoading">
                                <span v-if="!isLoading">{{ $t('Connect Whatsapp account') }}</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <form v-else @submit.prevent="submitForm()" class="overflow-y-auto md:w-[50%]"
                        :class="isCampaignFlow ? 'p-4 md:p-8 h-[90vh]' : ' h-full'">
                        <div v-if="displayTitle"
                            class="m-1 rounded px-3 pt-3 pb-3 bg-slate-100 flex items-center justify-between mb-4">
                            <h3 class="text-[15px]">{{ $t('Send Template Message') }}</h3>
                            <button @click="viewTemplate()"
                                class="text-sm md:inline-flex hidden justify-center rounded-md border border-transparent bg-red-800 px-4 py-1 text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">Cancel</button>
                        </div>
                        <div class="grid gap-x-6 gap-y-4 mb-8 px-3 pb-60"
                            :class="isCampaignFlow ? 'sm:grid-cols-6' : ''">
                            <FormInput v-if="isCampaignFlow" v-model="form.name" :name="$t('Campaign name')"
                                :type="'text'" :error="form.errors.name" :required="true" :class="'sm:col-span-6'" />
                            <FormSelect v-model="form.template" @update:modelValue="loadTemplate"
                                :options="templateOptions" :required="true" :error="form.errors.template"
                                :name="$t('Template')" :class="'sm:col-span-3'" :placeholder="$t('Select template')" />
                            <FormSelect v-if="isCampaignFlow" v-model="form.contacts" :options="contactGroupOptions"
                                :name="$t('Send to')" :required="true" :class="'sm:col-span-3'"
                                :placeholder="$t('Select contacts')" :error="form.errors.contacts" />
                            <FormInput v-if="isCampaignFlow && !form.skip_schedule" v-model="form.time"
                                :name="$t('Scheduled time')" :type="'datetime-local'" :error="form.errors.time"
                                :required="true" :class="'sm:col-span-6'" />
                            <div v-if="isCampaignFlow" class="relative flex gap-x-3 sm:col-span-6">
                                <div class="flex h-6 items-center">
                                    <input v-model="form.skip_schedule" id="skip-schedule" name="skip-schedule"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                </div>
                                <div class="text-sm leading-6">
                                    <label for="skip-schedule" class="font-medium text-gray-900">{{ $t(`Skip scheduling
                                        & send immediately`) }}</label>
                                </div>
                            </div>
                        </div>
                        <div :class="isCampaignFlow ? '' : 'px-3 md:px-3'">
                            <div v-if="form.header.parameters.length > 0" class="bg-slate-100 p-3 mt-4 text-sm">
                                <h2 class="flex items-center justify-between space-x-2 pb-2 border-b">
                                    <div class="flex items-center space-x-2">
                                        <span>{{ $t('Header variables') }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 1024 1024">
                                            <path fill="currentColor"
                                                d="M512 64a448 448 0 1 1 0 896.064A448 448 0 0 1 512 64zm67.2 275.072c33.28 0 60.288-23.104 60.288-57.344s-27.072-57.344-60.288-57.344c-33.28 0-60.16 23.104-60.16 57.344s26.88 57.344 60.16 57.344zM590.912 699.2c0-6.848 2.368-24.64 1.024-34.752l-52.608 60.544c-10.88 11.456-24.512 19.392-30.912 17.28a12.992 12.992 0 0 1-8.256-14.72l87.68-276.992c7.168-35.136-12.544-67.2-54.336-71.296c-44.096 0-108.992 44.736-148.48 101.504c0 6.784-1.28 23.68.064 33.792l52.544-60.608c10.88-11.328 23.552-19.328 29.952-17.152a12.8 12.8 0 0 1 7.808 16.128L388.48 728.576c-10.048 32.256 8.96 63.872 55.04 71.04c67.84 0 107.904-43.648 147.456-100.416z" />
                                        </svg>
                                    </div>
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M6.102 16.98c-1.074 0-1.648-1.264-.94-2.073l5.521-6.31a1.75 1.75 0 0 1 2.634 0l5.522 6.31c.707.809.133 2.073-.94 2.073H6.101Z" />
                                        </svg>
                                    </span>
                                </h2>

                                
                                <div v-for="(item, index) in form.header.parameters" :key="index"
                                    class="mt-2 flex items-center space-x-4">
                                    <div v-if="form.header.parameters[index].type === 'text'" class="w-full">
                                        <FormSelect v-model="form.header.parameters[index].selection"
                                            :name="$t('Content type')" :options="variableOptions"
                                            :class="'sm:col-span-6'" />
                                    </div>
                                    <div v-if="form.header.parameters[index].type === 'text'" class="w-full">
                                        <FormInput v-if="form.header.parameters[index].selection === 'static'"
                                            :name="$t('Value')" :required="true"
                                            :error="form.errors['header.parameters.0.value']"
                                            v-model="form.header.parameters[index].value" :type="'text'"
                                            :class="'sm:col-span-6'" />
                                        <FormSelect v-if="form.header.parameters[index].selection === 'dynamic'"
                                            :name="$t('Value')" :required="true"
                                            :error="form.errors['header.parameters.0.value']"
                                            v-model="form.header.parameters[index].value" :options="dynamicOptions"
                                            :class="'sm:col-span-6'" />
                                    </div>
                                    <div v-if="['IMAGE', 'DOCUMENT', 'VIDEO'].includes(form.header.parameters[index].type)"
                                        class="w-full mt-3">
                                        <div>
                                            <div class="flex items-center space-x-4">
                                                <label
                                                    class="cursor-pointer flex justify-center px-2 py-2 w-[30%] bg-slate-200 shadow-sm rounded-lg border"
                                                    :class="form.errors['header.parameters.0.value'] ? 'border border-red-700' : ''"
                                                    for="file-upload">
                                                    {{ $t('Upload') }}
                                                </label>
                                                <input type="file" class="sr-only"
                                                    :accept="getFileAcceptAttribute(form.header.parameters[index].type)"
                                                    ref="fileInput" id="file-upload" @change="handleFileUpload" />
                                                <div v-if="form.header.parameters[index].value"
                                                    class="w-[20em] truncate">{{ form.header.parameters[index].selection
                                                        === 'default' ? form.header.parameters[index].value :
                                                        form.header.parameters[index].value.name }}</div>
                                                <span v-else>{{ $t('No file chosen') }}</span>
                                            </div>
                                            <p v-if="form.header.parameters[index].type === 'IMAGE'"
                                                class="text-left text-xs mt-2">{{ $t('Max file upload size is') }}
                                                <b>5MB</b> <br> {{ $t('Supported file extensions') }}: .png, jpg
                                            </p>
                                            <p v-if="form.header.parameters[index].type === 'DOCUMENT'"
                                                class="text-left text-xs mt-2">{{ $t('Max file upload size is') }}
                                                <b>100MB</b> <br> {{ $t('Supported file extensions') }}: .pdf, .txt,
                                                .ppt, .doc, .xls, .docx, .pptx, .xlsx
                                            </p>
                                            <p v-if="form.header.parameters[index].type === 'VIDEO'"
                                                class="text-left text-xs mt-2">{{ $t('Max file upload size is') }}
                                                <b>16MB</b> <br> {{ $t('Supported file extensions') }}: .mp4
                                            </p>
                                        </div>

                                        <div v-if="form.errors['header.parameters.0.value']"
                                            class="form-error text-[#b91c1c] text-xs">{{
                                                form.errors['header.parameters.0.value'] }}</div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="form.body.parameters.length > 0" class="bg-slate-100 p-3 mt-1 text-sm">
                                <h2 class="flex items-center justify-between space-x-2 pb-2 border-b">
                                    <div class="flex items-center space-x-2">
                                        <span>{{ $t('Body variables') }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 1024 1024">
                                            <path fill="currentColor"
                                                d="M512 64a448 448 0 1 1 0 896.064A448 448 0 0 1 512 64zm67.2 275.072c33.28 0 60.288-23.104 60.288-57.344s-27.072-57.344-60.288-57.344c-33.28 0-60.16 23.104-60.16 57.344s26.88 57.344 60.16 57.344zM590.912 699.2c0-6.848 2.368-24.64 1.024-34.752l-52.608 60.544c-10.88 11.456-24.512 19.392-30.912 17.28a12.992 12.992 0 0 1-8.256-14.72l87.68-276.992c7.168-35.136-12.544-67.2-54.336-71.296c-44.096 0-108.992 44.736-148.48 101.504c0 6.784-1.28 23.68.064 33.792l52.544-60.608c10.88-11.328 23.552-19.328 29.952-17.152a12.8 12.8 0 0 1 7.808 16.128L388.48 728.576c-10.048 32.256 8.96 63.872 55.04 71.04c67.84 0 107.904-43.648 147.456-100.416z" />
                                        </svg>
                                    </div>
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M6.102 16.98c-1.074 0-1.648-1.264-.94-2.073l5.521-6.31a1.75 1.75 0 0 1 2.634 0l5.522 6.31c.707.809.133 2.073-.94 2.073H6.101Z" />
                                        </svg>
                                    </span>
                                </h2>
                                <div v-for="(item, index) in form.body.parameters" :key="index"
                                    class="mt-2 flex items-center space-x-4">
                                    <div class="w-[30%]">
                                        <span v-text="'{{' + (index + 1) + '}}'"></span>
                                    </div>
                                    <div class="w-full">
                                        <FormSelect v-model="form.body.parameters[index].selection"
                                            :options="variableOptions" :class="'sm:col-span-6'" />
                                    </div>
                                    <div class="w-full">
                                        <FormInput v-if="form.body.parameters[index].selection === 'static'"
                                            v-model="form.body.parameters[index].value" :required="true"
                                            :error="form.errors['body.parameters.0.value']" :type="'text'"
                                            :class="'sm:col-span-6'" />
                                        <FormSelect v-if="form.body.parameters[index].selection === 'dynamic'"
                                            v-model="form.body.parameters[index].value" :required="true"
                                            :error="form.errors['body.parameters.0.value']" :options="dynamicOptions"
                                            :class="'sm:col-span-6'" />
                                    </div>
                                </div>
                            </div>
                            <div v-if="form.buttons.length > 0" class="bg-slate-100 p-3 mt-1 text-sm">
                                <h2 class="flex items-center justify-between space-x-2 pb-2 border-b">
                                    <div class="flex items-center space-x-2">
                                        <span>{{ $t('Button variables') }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 1024 1024">
                                            <path fill="currentColor"
                                                d="M512 64a448 448 0 1 1 0 896.064A448 448 0 0 1 512 64zm67.2 275.072c33.28 0 60.288-23.104 60.288-57.344s-27.072-57.344-60.288-57.344c-33.28 0-60.16 23.104-60.16 57.344s26.88 57.344 60.16 57.344zM590.912 699.2c0-6.848 2.368-24.64 1.024-34.752l-52.608 60.544c-10.88 11.456-24.512 19.392-30.912 17.28a12.992 12.992 0 0 1-8.256-14.72l87.68-276.992c7.168-35.136-12.544-67.2-54.336-71.296c-44.096 0-108.992 44.736-148.48 101.504c0 6.784-1.28 23.68.064 33.792l52.544-60.608c10.88-11.328 23.552-19.328 29.952-17.152a12.8 12.8 0 0 1 7.808 16.128L388.48 728.576c-10.048 32.256 8.96 63.872 55.04 71.04c67.84 0 107.904-43.648 147.456-100.416z" />
                                        </svg>
                                    </div>
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M6.102 16.98c-1.074 0-1.648-1.264-.94-2.073l5.521-6.31a1.75 1.75 0 0 1 2.634 0l5.522 6.31c.707.809.133 2.073-.94 2.073H6.101Z" />
                                        </svg>
                                    </span>
                                </h2>
                                <div v-for="(item, index) in form.buttons" :key="index">
                                    <div v-if="item.parameters.length > 0" class="mt-4 bg-slate-50 p-3">
                                        <div class="w-[100%] mb-1">
                                            <span>{{ $t('Label') }}: {{ item.text }}</span>
                                        </div>
                                        <div v-for="(value, key) in item.parameters" :key="key"
                                            class="flex items-center space-x-4">
                                            <div class="w-full">
                                                <FormSelect v-model="value.type" :name="$t('Button type')"
                                                    :options="variableOptions" :class="'sm:col-span-6'" />
                                            </div>
                                            <div class="w-full">
                                                <FormInput v-if="value.type === 'static'" v-model="value.value"
                                                    :name="$t('Value')" :required="true"
                                                    :error="form.errors['buttons.' + index + '.parameters.0.value']"
                                                    :type="'text'" :class="'sm:col-span-6'" />
                                                <FormSelect v-if="value.type === 'dynamic'" v-model="value.value"
                                                    :name="$t('Value')" :required="true"
                                                    :error="form.errors['buttons.' + index + '.parameters.0.value']"
                                                    :options="dynamicOptions" :class="'sm:col-span-6'" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-end space-x-3 mt-3">
                                <div v-if="displayCancelBtn">
                                    <Link href="/campaigns"
                                        class="block rounded-md px-3 py-2 text-sm text-gray-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 bg-slate-200">
                                    {{ $t('Cancel') }}
                                    </Link>
                                </div>
                                <div class="pb-100">
                                    <button type="submit"
                                        class="rounded-md px-3 py-2 text-sm text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 bg-primary"
                                        :disabled="isLoading">
                                        <span v-if="!isLoading">{{ sendText ? sendText : $t('Save') }}</span>
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                                opacity=".5" />
                                            <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                                                <animateTransform attributeName="transform" dur="1s" from="0 12 12"
                                                    repeatCount="indefinite" to="360 12 12" type="rotate" />
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="md:w-[50%] py-20 flex justify-center chat-bg"
                        :class="isCampaignFlow ? 'px-20' : 'px-10'">
                        <div>
                            <WhatsappTemplate :parameters="form" :visible="form.template ? true : false" />
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </component>
</template> -->


<!-- ===================================== NEW UI CODE ===================================== -->

<script setup>
import axios from "axios";
import FormInput from '@/Components/FormInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import WhatsappTemplate from '@/Components/WhatsappTemplate.vue';
import { ref, computed, onMounted } from 'vue';
import { Link, router, useForm } from "@inertiajs/vue3";
import 'vue3-toastify/dist/index.css';
import { trans } from 'laravel-vue-i18n';
import Backup from './CrouselSend.vue';
import FlowTemplate from './FlowTemplate.vue';
import { MessageSquare, Send, Clock, FileText, Image, Video, FileIcon, ChevronDown, ChevronUp, Sparkles, Zap, X } from 'lucide-vue-next';

const props = defineProps({
    templates: Object,
    contactGroups: Object,
    settings: Array,
    contact: {
        type: String,
        default: null
    },
    displayTitle: {
        type: Boolean,
        default: false
    },
    displayCancelBtn: {
        type: Boolean,
        default: true
    },
    isCampaignFlow: {
        type: Boolean,
        default: true
    },
    sendText: {
        type: String,
        default: 'Save'
    }
});

const isLoading = ref(false);
const contactGroupOptions = ref([
    { value: 'all', label: trans('all contacts') },
]);
const templateOptions = ref([]);
const config = ref(props.settings?.metadata);
const settings = ref(config.value ? JSON.parse(config.value) : null);
const showModal = ref(true);
const userChoice = ref(null);
const expandedSections = ref({
    header: true,
    body: true,
    buttons: true
});

const sharedProps = computed(() => ({
    templates: props.templates,
    contactGroups: props.contactGroups,
    settings: props.settings,
    contact: props.contact,
    displayTitle: props.displayTitle,
    displayCancelBtn: props.displayCancelBtn,
    isCampaignFlow: props.isCampaignFlow,
    sendText: props.sendText,
    form,
    settingsParsed: settings.value,
    isLoading: isLoading.value,
    contactGroupOptions: contactGroupOptions.value,
    templateOptions: templateOptions.value,
    variableOptions: variableOptions.value,
    dynamicOptions: dynamicOptions.value,
}));

const chooseOption = (option) => {
    userChoice.value = option;
    showModal.value = false;
};

const variableOptions = ref([
    { value: 'static', label: trans('static') },
    { value: 'dynamic', label: trans('dynamic') }
]);

const dynamicOptions = ref([
    { value: 'first name', label: trans('contact first name') },
    { value: 'last name', label: trans('contact last name') },
    { value: 'name', label: trans('contact full name') },
    { value: 'phone', label: trans('contact phone') },
    { value: 'email', label: trans('contact email') },
]);

const form = useForm({
    name: null,
    template: null,
    contacts: null,
    time: null,
    skip_schedule: false,
    'header': {
        'format': null,
        'text': null,
        'parameters': []
    },
    'body': {
        'text': null,
        'parameters': []
    },
    'footer': {
        'text': null,
    },
    'buttons': [],
});

const toggleSection = (section) => {
    expandedSections.value[section] = !expandedSections.value[section];
};

const loadTemplate = async () => {
    try {
        const response = await axios.get('/templates/' + form.template);
        if (response) {
            const metadata = JSON.parse(response.data.metadata);
            form.header.format = extractComponent(metadata, 'HEADER', 'format');

            form.header.text = extractComponent(metadata, 'HEADER', 'text');
            const headerExamples = extractComponent(metadata, 'HEADER', 'example');
            if (headerExamples) {
                if (form.header.format === 'TEXT') {
                    form.header.parameters = headerExamples.header_text.map(item => ({
                        type: 'text',
                        selection: 'static',
                        value: item,
                    }));
                } else if (form.header.format === 'IMAGE' || form.header.format === 'DOCUMENT' || form.header.format === 'VIDEO') {
                    form.header.parameters = headerExamples.header_handle.map(item => ({
                        type: form.header.format,
                        selection: 'default',
                        value: null,
                        url: item,
                    }));
                }
            } else {
                form.header.parameters = [];
            }

            form.body.text = extractComponent(metadata, 'BODY', 'text');
            const bodyExamples = extractComponent(metadata, 'BODY', 'example');
            if (bodyExamples) {
                form.body.parameters = bodyExamples.body_text[0].map(item => ({
                    type: 'text',
                    selection: 'static',
                    value: item,
                }));
            } else {
                form.body.parameters = [];
            }

            form.footer.text = extractComponent(metadata, 'FOOTER', 'text');

            const buttons = extractComponent(metadata, 'BUTTONS', 'buttons');
            if (buttons) {
                form.buttons = buttons.map(item => ({
                    type: item.type,
                    text: item.text,
                    value: item[item.type.toLowerCase()] ?? null,
                    parameters: (item.type === 'QUICK_REPLY')
                        ? [{ type: 'static', value: null }]
                        : (item.example
                            ? item.example.map(param => ({ type: 'static', value: param }))
                            : []
                        ),
                }));
            } else {
                form.buttons = [];
            }
        }
    } catch (error) {
        console.error('Error loading template:', error);
    }
}

const handleFileUpload = (event) => {
    const fileSizeLimit = getFileSizeLimit(form.header.parameters[0].type);
    const file = event.target.files[0];

    if (file && file.size > fileSizeLimit) {
        alert(trans('file size exceeds the limit. Max allowed size:') + ' ' + fileSizeLimit + 'b');
        event.target.value = null;
    } else {
        const reader = new FileReader();

        reader.onload = (e) => {
            form.header.parameters[0].url = e.target.result;
        };

        form.header.parameters[0].selection = 'upload';
        form.header.parameters[0].value = file;

        reader.readAsDataURL(file);
    }
}

const getFileAcceptAttribute = (fileType) => {
    switch (fileType) {
        case 'IMAGE':
            return '.png, .jpg';
        case 'DOCUMENT':
            return '.pdf, .txt, .ppt, .doc, .xls, .docx, .pptx, .xlsx';
        case 'VIDEO':
            return '.mp4';
        default:
            return '';
    }
}

const getFileSizeLimit = (fileType) => {
    switch (fileType) {
        case 'IMAGE':
            return 5 * 1024 * 1024;
        case 'DOCUMENT':
            return 100 * 1024 * 1024;
        case 'VIDEO':
            return 16 * 1024 * 1024;
        default:
            return Infinity;
    }
}

const extractComponent = (data, type, customProperty) => {
    const component = data.components.find(
        (c) => c.type === type
    );

    return component ? component[customProperty] : null;
};

const transformOptions = (options) => {
    return options.map((option) => ({
        value: option.uuid,
        label: option.language ? option.name + ' [' + option.language + ']' : option.name,
    }));
};

const submitForm = () => {
    isLoading.value = true;
    form.post(props.isCampaignFlow ? '/campaigns' : '/chat/' + props.contact + '/send/template', {
        onFinish: () => {
            isLoading.value = false;
            if (!props.isCampaignFlow) {
                emit('viewTemplate', false);
            }
        },
    });
}

const emit = defineEmits(['viewTemplate']);

const viewTemplate = () => {
    emit('viewTemplate', false);
}

onMounted(() => {
    const filteredTemplates = props.templates.filter(template =>
        !template.name?.startsWith('crousel_') && !template.name?.startsWith('flow_')
    );

    templateOptions.value = transformOptions(filteredTemplates);

    contactGroupOptions.value = [
        ...contactGroupOptions.value,
        ...transformOptions(props.contactGroups)
    ];
});

const closeModal = () => {
    showModal.value = false;
    emit('viewTemplate', false);
    if (props.isCampaignFlow) {
        router.visit('/campaigns');
    }
}

</script>

<template>
    <!-- Enhanced Modal with Glassmorphism -->
    <div v-if="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-md bg-black/60 animate-in fade-in duration-300">
        <div
            class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all animate-in zoom-in duration-300">
            <!-- Modal Header -->
            <div class="bg-gradient-to-br from-[#ff5100] to-[#ff7433] p-8 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mr-20 -mt-20"></div>
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/10 rounded-full -ml-16 -mb-16"></div>
                <div class="absolute top-8 right-8 z-[9999] ">
                    <button @click="closeModal"
                        class="p-1 bg-black/20 hover:bg-black/10 backdrop-blur-sm text-white rounded-lg transition-colors cursor-pointer">
                        <X class="w-6 h-6 cursor-pointer" />
                    </button>
                </div>

                <div class="relative z-10">
                    <div class="flex items-center justify-center mb-3">
                        <Sparkles class="w-8 h-8 animate-pulse" />
                    </div>
                    <h2 class="text-2xl font-bold text-center">Choose Campaign Type</h2>
                    <p class="text-center text-white/90 mt-2 text-sm">Select the type of message you want to send</p>
                </div>
            </div>

            <!-- Modal Options -->
            <div class="p-6 space-y-3">
                <button @click="chooseOption('template')"
                    class="group w-full bg-gradient-to-r from-[#ff5100] to-[#ff6422] hover:from-[#ff6422] hover:to-[#ff5100] text-white py-4 px-6 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-[1.02] flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <MessageSquare class="w-5 h-5" />
                        <div class="text-left">
                            <div class="font-semibold">Template Message</div>
                            <div class="text-xs text-white/80">Send pre-approved templates</div>
                        </div>
                    </div>
                    <Zap class="w-5 h-5 group-hover:rotate-12 transition-transform" />
                </button>

                <button @click="chooseOption('carousel')"
                    class="group w-full bg-gradient-to-r from-[#ff5100] to-[#ff6422] hover:from-[#ff6422] hover:to-[#ff5100] text-white py-4 px-6 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-[1.02] flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <Image class="w-5 h-5" />
                        <div class="text-left">
                            <div class="font-semibold">Carousel Message</div>
                            <div class="text-xs text-white/80">Send multiple cards</div>
                        </div>
                    </div>
                    <Zap class="w-5 h-5 group-hover:rotate-12 transition-transform" />
                </button>

                <button @click="chooseOption('flow')"
                    class="group w-full bg-gradient-to-r from-[#ff5100] to-[#ff6422] hover:from-[#ff6422] hover:to-[#ff5100] text-white py-4 px-6 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-[1.02] flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <Sparkles class="w-5 h-5" />
                        <div class="text-left">
                            <div class="font-semibold">Flow Message</div>
                            <div class="text-xs text-white/80">Interactive flow campaigns</div>
                        </div>
                    </div>
                    <Zap class="w-5 h-5 group-hover:rotate-12 transition-transform" />
                </button>
            </div>
        </div>
    </div>

    <component v-if="userChoice === 'flow'" :is="FlowTemplate" v-bind="sharedProps" />
    <component v-else :is="userChoice === 'carousel' ? Backup : 'div'" v-bind="sharedProps">
        <template v-if="userChoice === 'template'">
            <div class="w-full lg:h-screen bg-[#EEEEEE] flex flex-col lg:flex-row overflow-hidden"
                :class="isCampaignFlow ? `lg:max-h-[calc(100vh-170px)]` : `lg:max-h-[calc(100vh-145px)]`">
                <!-- Left Panel - Form -->
                <div class="w-full lg:w-1/2 overflow-y-auto">
                    <!-- Not Connected State -->
                    <div v-if="!settings?.whatsapp" class="p-8 flex items-center justify-center min-h-screen">
                        <div class="max-w-md w-full">
                            <div
                                class="bg-white rounded-3xl shadow-xl p-8 border border-slate-200 relative overflow-hidden">
                                <div
                                    class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-[#ff5100]/10 to-transparent rounded-full -mr-16 -mt-16">
                                </div>

                                <div class="relative z-10">
                                    <div
                                        class="w-20 h-20 bg-gradient-to-br from-[#ff5100] to-[#ff6422] rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                                        <MessageSquare class="w-10 h-10 text-white" />
                                    </div>

                                    <h3 class="text-2xl font-bold text-center mb-3 text-slate-800">
                                        {{ $t('Connect WhatsApp') }}
                                    </h3>
                                    <p class="text-center text-slate-600 mb-6 leading-relaxed">
                                        {{ $t(`You need to connect your WhatsApp account first before you can send
                                        out campaigns.`) }}
                                    </p>
                                    <Link href="/settings/whatsapp"
                                        class="w-full flex items-center justify-center space-x-2 bg-gradient-to-r from-[#ff5100] to-[#ff6422] hover:from-[#ff6422] hover:to-[#ff5100] text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-[1.02]">
                                    <Zap class="w-5 h-5" />
                                    <span>{{ $t('Connect Whatsapp account') }}</span>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Content -->
                    <form v-else @submit.prevent="submitForm()" class="p-6 lg:p-8 space-y-6">
                        <!-- Header Card -->
                        <div v-if="displayTitle" class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <!-- <div
                                        class="w-10 h-10 bg-gradient-to-br from-[#ff5100] to-[#ff6422] rounded-xl flex items-center justify-center">
                                        <Send class="w-4 h-4 text-white" />
                                    </div> -->
                                    <h3 class="text-sm font-bold text-slate-800">{{ $t('Send Template Message') }}
                                    </h3>
                                </div>
                                <button @click="viewTemplate()" type="button"
                                    class="px-3 py-1.5 bg-primary hover:bg-primary/90 text-white rounded-lg transition-colors duration-200 flex items-center space-x-2">
                                    <X class="w-4 h-4" />
                                    <span class="hidden md:inline">Cancel</span>
                                </button>
                            </div>
                        </div>

                        <!-- Campaign Details Card -->
                        <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 space-y-5">
                            <div class="flex items-center space-x-2 mb-4">
                                <FileText class="w-5 h-5 text-[#ff5100]" />
                                <h4 class="font-semibold text-slate-800">Campaign Details</h4>
                            </div>

                            <FormInput v-if="isCampaignFlow" v-model="form.name" :name="$t('Campaign name')"
                                :type="'text'" :error="form.errors.name" :required="true" :class="'w-full'" />

                            <div class="grid grid-cols-1 gap-4">
                                <FormSelect v-model="form.template" @update:modelValue="loadTemplate"
                                    :options="templateOptions" :required="true" :error="form.errors.template"
                                    :name="$t('Template')" :placeholder="$t('Select template')" />

                                <FormSelect v-if="isCampaignFlow" v-model="form.contacts" :options="contactGroupOptions"
                                    :name="$t('Send to')" :required="true" :placeholder="$t('Select contacts')"
                                    :error="form.errors.contacts" />
                            </div>

                            <FormInput v-if="isCampaignFlow && !form.skip_schedule" v-model="form.time"
                                :name="$t('Scheduled time')" :type="'datetime-local'" :error="form.errors.time"
                                :required="true" :class="'w-full'" />

                            <div v-if="isCampaignFlow"
                                class="flex items-center space-x-3 p-4 bg-slate-50 rounded-xl border border-slate-200">
                                <input v-model="form.skip_schedule" id="skip-schedule" type="checkbox"
                                    class="w-5 h-5 rounded border-slate-300 text-[#ff5100] focus:ring-[#ff5100] focus:ring-2 cursor-pointer">
                                <label for="skip-schedule"
                                    class="text-sm font-medium text-slate-700 cursor-pointer flex items-center space-x-2">
                                    <Clock class="w-4 h-4" />
                                    <span>{{ $t('Skip scheduling & send immediately') }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Header Variables -->
                        <div v-if="form.header.parameters.length > 0"
                            class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                            <button type="button" @click="toggleSection('header')"
                                class="w-full p-5 flex items-center justify-between hover:bg-slate-50 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-[#ff5100]/10 rounded-lg flex items-center justify-center">
                                        <Image class="w-4 h-4 text-[#ff5100]" />
                                    </div>
                                    <h4 class="font-semibold text-slate-800">{{ $t('Header Variables') }}</h4>
                                    <span
                                        class="px-2 py-1 bg-[#ff5100]/10 text-[#ff5100] text-xs font-medium rounded-full">
                                        {{ form.header.parameters.length }}
                                    </span>
                                </div>
                                <component :is="expandedSections.header ? ChevronUp : ChevronDown"
                                    class="w-5 h-5 text-slate-400 transition-transform" />
                            </button>

                            <div v-show="expandedSections.header" class="p-5 pt-0 space-y-4 border-t border-slate-100">
                                <div v-for="(item, index) in form.header.parameters" :key="index"
                                    class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">

                                    <template v-if="item.type === 'text'">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <FormSelect v-model="form.header.parameters[index].selection"
                                                :name="$t('Content type')" :options="variableOptions" />

                                            <FormInput v-if="form.header.parameters[index].selection === 'static'"
                                                :name="$t('Value')" :required="true"
                                                :error="form.errors['header.parameters.0.value']"
                                                v-model="form.header.parameters[index].value" :type="'text'" />

                                            <FormSelect v-if="form.header.parameters[index].selection === 'dynamic'"
                                                :name="$t('Value')" :required="true"
                                                :error="form.errors['header.parameters.0.value']"
                                                v-model="form.header.parameters[index].value"
                                                :options="dynamicOptions" />
                                        </div>
                                    </template>

                                    <template v-if="['IMAGE', 'DOCUMENT', 'VIDEO'].includes(item.type)">
                                        <div class="space-y-3">
                                            <div class="flex items-center space-x-3">
                                                <label for="file-upload"
                                                    class="cursor-pointer flex items-center justify-center space-x-2 px-4 py-2 bg-gradient-to-r from-[#ff5100] to-[#ff6422] text-white rounded-lg hover:shadow-lg transition-all duration-200">
                                                    <component
                                                        :is="item.type === 'IMAGE' ? Image : item.type === 'VIDEO' ? Video : FileIcon"
                                                        class="w-4 h-4" />
                                                    <span>{{ $t('Upload') }}</span>
                                                </label>
                                                <input type="file" class="sr-only"
                                                    :accept="getFileAcceptAttribute(item.type)" id="file-upload"
                                                    @change="handleFileUpload" />
                                                <span v-if="item.value" class="text-sm text-slate-600 truncate flex-1">
                                                    {{ item.selection === 'default' ? item.value : item.value.name
                                                    }}
                                                </span>
                                                <span v-else class="text-sm text-slate-400">{{ $t('No file chosen')
                                                }}</span>
                                            </div>
                                            <p class="text-xs text-slate-500 flex items-start space-x-2">
                                                <span class="text-[#ff5100]">ℹ</span>
                                                <span v-if="item.type === 'IMAGE'">
                                                    {{ $t('Max file upload size is') }} <b>5MB</b> | Supported:
                                                    .png, .jpg
                                                </span>
                                                <span v-if="item.type === 'DOCUMENT'">
                                                    {{ $t('Max file upload size is') }} <b>100MB</b> | Supported:
                                                    .pdf, .txt, .ppt, .doc, .xls, .docx, .pptx, .xlsx
                                                </span>
                                                <span v-if="item.type === 'VIDEO'">
                                                    {{ $t('Max file upload size is') }} <b>16MB</b> | Supported:
                                                    .mp4
                                                </span>
                                            </p>
                                            <div v-if="form.errors['header.parameters.0.value']"
                                                class="text-red-600 text-xs">
                                                {{ form.errors['header.parameters.0.value'] }}
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Body Variables -->
                        <div v-if="form.body.parameters.length > 0"
                            class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                            <button type="button" @click="toggleSection('body')"
                                class="w-full p-5 flex items-center justify-between hover:bg-slate-50 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-[#ff5100]/10 rounded-lg flex items-center justify-center">
                                        <FileText class="w-4 h-4 text-[#ff5100]" />
                                    </div>
                                    <h4 class="font-semibold text-slate-800">{{ $t('Body Variables') }}</h4>
                                    <span
                                        class="px-2 py-1 bg-[#ff5100]/10 text-[#ff5100] text-xs font-medium rounded-full">
                                        {{ form.body.parameters.length }}
                                    </span>
                                </div>
                                <component :is="expandedSections.body ? ChevronUp : ChevronDown"
                                    class="w-5 h-5 text-slate-400 transition-transform" />
                            </button>

                            <div v-show="expandedSections.body" class="p-5 pt-0 space-y-3 border-t border-slate-100">
                                <div v-for="(item, index) in form.body.parameters" :key="index"
                                    class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                                    <div class="grid grid-cols-12 gap-3 items-end">
                                        <div class="col-span-2 flex items-center justify-center">
                                            <span
                                                class="px-3 py-1 bg-white border border-slate-300 rounded-lg font-mono text-sm font-semibold text-slate-700"
                                                v-text="'{{' + (index + 1) + '}}'"></span>
                                        </div>
                                        <div class="col-span-5">
                                            <FormSelect v-model="form.body.parameters[index].selection"
                                                :options="variableOptions" />
                                        </div>
                                        <div class="col-span-5">
                                            <FormInput v-if="form.body.parameters[index].selection === 'static'"
                                                v-model="form.body.parameters[index].value" :required="true"
                                                :error="form.errors['body.parameters.0.value']" :type="'text'" />
                                            <FormSelect v-if="form.body.parameters[index].selection === 'dynamic'"
                                                v-model="form.body.parameters[index].value" :required="true"
                                                :error="form.errors['body.parameters.0.value']"
                                                :options="dynamicOptions" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Button Variables -->
                        <div v-if="form.buttons.length > 0"
                            class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                            <button type="button" @click="toggleSection('buttons')"
                                class="w-full p-5 flex items-center justify-between hover:bg-slate-50 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-[#ff5100]/10 rounded-lg flex items-center justify-center">
                                        <Zap class="w-4 h-4 text-[#ff5100]" />
                                    </div>
                                    <h4 class="font-semibold text-slate-800">{{ $t('Button Variables') }}</h4>
                                    <span
                                        class="px-2 py-1 bg-[#ff5100]/10 text-[#ff5100] text-xs font-medium rounded-full">
                                        {{ form.buttons.length }}
                                    </span>
                                </div>
                                <component :is="expandedSections.buttons ? ChevronUp : ChevronDown"
                                    class="w-5 h-5 text-slate-400 transition-transform" />
                            </button>

                            <div v-show="expandedSections.buttons" class="p-5 pt-0 space-y-4 border-t border-slate-100">
                                <div v-for="(item, index) in form.buttons" :key="index">
                                    <div v-if="item.parameters.length > 0"
                                        class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                                        <div class="flex items-center space-x-2 pb-2 border-b border-slate-200">
                                            <span
                                                class="px-3 py-1 bg-white rounded-lg text-sm font-medium text-slate-700">
                                                {{ $t('Label') }}: {{ item.text }}
                                            </span>
                                        </div>
                                        <div v-for="(value, key) in item.parameters" :key="key"
                                            class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <FormSelect v-model="value.type" :name="$t('Button type')"
                                                :options="variableOptions" />

                                            <FormInput v-if="value.type === 'static'" v-model="value.value"
                                                :name="$t('Value')" :required="true"
                                                :error="form.errors['buttons.' + index + '.parameters.0.value']"
                                                :type="'text'" />

                                            <FormSelect v-if="value.type === 'dynamic'" v-model="value.value"
                                                :name="$t('Value')" :required="true"
                                                :error="form.errors['buttons.' + index + '.parameters.0.value']"
                                                :options="dynamicOptions" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end space-x-3 sticky bottom-0 bg-white p-6 rounded-2xl">
                            <Link v-if="displayCancelBtn" href="/campaigns"
                                class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-all duration-200 border border-slate-300">
                            {{ $t('Cancel') }}
                            </Link>

                            <button type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff6422] hover:from-[#ff6422] hover:to-[#ff5100] text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
                                :disabled="isLoading">
                                <template v-if="!isLoading">
                                    <Send class="w-4 h-4" />
                                    <span>{{ sendText ? sendText : $t('Save') }}</span>
                                </template>
                                <template v-else>
                                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span>{{ $t('Sending...') }}</span>
                                </template>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right Panel - Preview -->
                <div
                    class="w-full lg:w-1/2 bg-gradient-to-br from-slate-100 via-orange-50/20 to-slate-100 p-4 flex items-center justify-center relative">
                    <!-- Background Decorations -->
                    <div v-if="isCampaignFlow"
                        class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-[#ff5100]/5 to-transparent rounded-full blur-3xl">
                    </div>
                    <div v-if="isCampaignFlow"
                        class="absolute bottom-0 left-0 w-80 h-80 bg-gradient-to-tr from-orange-200/20 to-transparent rounded-full blur-3xl">
                    </div>

                    <!-- Preview Container -->
                    <div class="lg:sticky top-0 lg:z-10">
                        <div class="text-center my-6">
                            <div
                                class="inline-flex items-center space-x-2 px-4 py-2 bg-white/80 backdrop-blur-sm rounded-full shadow-sm border border-slate-200 mb-3">
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                <span class="text-sm font-medium text-slate-700">Live Preview</span>
                            </div>
                            <h3 class="text-xl font-bold text-slate-800 mb-1">WhatsApp Message Preview</h3>
                            <p class="text-sm text-slate-600">See how your message will appear</p>
                        </div>

                        <!-- Phone Frame -->
                        <div class="bg-white rounded-3xl shadow-2xl p-0 border-8 border-slate-800 relative">
                            <!-- Phone Notch -->
                            <div
                                class="absolute z-50 top-0 left-1/2 -translate-x-1/2 w-24 h-3 bg-slate-800 rounded-b-2xl">
                            </div>

                            <!-- WhatsApp Preview -->
                            <div
                                class="w-full chat-bg pt-4 rounded-2xl overflow-hidden h-[calc(100vh-380px)] aspect-[12/16]">
                                <div class="overflow-y-auto h-full p-4">
                                    <WhatsappTemplate :parameters="form" :visible="form.template ? true : false" />

                                    <!-- Empty State -->
                                    <div v-if="!form.template"
                                        class="flex flex-col items-center justify-center h-full text-center px-6">
                                        <div
                                            class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-4">
                                            <MessageSquare class="w-10 h-10 text-green-500" />
                                        </div>
                                        <h4 class="font-semibold text-green-600 mb-2">No Template Selected</h4>
                                        <p class="text-sm text-black">Select a template to preview your message
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Info -->
                        <div class="my-4 text-center">
                            <p class="text-xs text-slate-500">
                                Preview may differ slightly from actual WhatsApp appearance
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </component>
</template>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

@keyframes zoom-in {
    from {
        transform: scale(0.95);
    }

    to {
        transform: scale(1);
    }
}

.animate-in {
    animation-duration: 0.3s;
    animation-timing-function: ease-out;
    animation-fill-mode: both;
}

.fade-in {
    animation-name: fade-in;
}

.zoom-in {
    animation-name: zoom-in;
}


/* Smooth transitions */
* {
    transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

button,
a {
    transition-duration: 200ms;
}

/* Loading spinner animation */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Pulse animation for live indicator */
@keyframes pulse {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Hover effects for cards */
.hover\:scale-\[1\.02\]:hover {
    transform: scale(1.02);
}

/* Focus states */
input:focus,
select:focus,
textarea:focus {
    outline: 2px solid #ff5100;
    outline-offset: 2px;
}

/* Checkbox custom styling */
input[type="checkbox"]:checked {
    background-color: #ff5100;
    border-color: #ff5100;
}

/* Disabled state */
button:disabled {
    cursor: not-allowed;
    opacity: 0.5;
}

/* Gradient text effect */
.gradient-text {
    background: linear-gradient(135deg, #ff5100, #ff6422);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Card hover effects */
.bg-white:hover {
    box-shadow: 0 10px 25px -5px rgba(255, 81, 0, 0.1), 0 8px 10px -6px rgba(255, 81, 0, 0.1);
}

/* Mobile responsiveness improvements */
@media (max-width: 768px) {
    .grid-cols-12 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .col-span-2,
    .col-span-5 {
        grid-column: span 12 / span 12;
    }
}
</style>
