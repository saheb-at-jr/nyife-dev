<!-- <script setup>
    import { ref } from 'vue';
    import { Link, useForm } from "@inertiajs/vue3";
    import FormCheckbox from '@/Components/FormCheckbox.vue';
    import FormInput from '@/Components/FormInput.vue';
    import FormPhoneInput from '@/Components/FormPhoneInput.vue';
    import FormSelect from '@/Components/FormSelect.vue';
    import FormTextArea from '@/Components/FormTextArea.vue';
    import { trans } from 'laravel-vue-i18n';

    const props = defineProps(['contactGroups', 'contact', 'fields', 'locationSettings']);
    const fileUrl = props.contact?.avatar ? ref(props.contact?.avatar) : ref(null);
    const inputFields = props.fields;

    const defaultFields = inputFields.reduce((acc, field) => {
        acc[field.name] = "";
        return acc;
    }, {});

    const getAddressDetail = (value, key) => {
        const address = JSON.parse(value);
        return address?.[key] ?? '';
    }

    const form = useForm({
        first_name: props.contact?.first_name ?? null,
        last_name: props.contact?.last_name ?? null,
        phone: props.contact?.phone ?? null,
        email: props.contact?.email ?? null,
        group: props.contact?.contact_groups ? props.contact.contact_groups.map(group => group.uuid || group) : [],
        file: null,
        street: props.contact?.address ? getAddressDetail(props.contact?.address, 'street') : null,
        city: props.contact?.address ? getAddressDetail(props.contact?.address, 'city') : null,
        state: props.contact?.address ? getAddressDetail(props.contact?.address, 'state') : null,
        zip: props.contact?.address ? getAddressDetail(props.contact?.address, 'zip') : null,
        country: props.contact?.address ? getAddressDetail(props.contact?.address, 'country') : null,
        metadata: props.contact?.metadata ? JSON.parse(props.contact?.metadata) : defaultFields,
    });

    const contactGroupOptions = () => {
        return props.contactGroups.map((option) => ({
            value: option.uuid,
            label: option.name,
        }));
    };

    const handleFileUpload = (event) => {
        const fileSizeLimit = 5 * 1024 * 1024; ///5MB
        const file = event.target.files[0];

        if (file && file.size > fileSizeLimit) {
            // Handle file size exceeding the limit
            alert(trans('file size exceeds the limit. Max allowed size:') + ' ' + fileSizeLimit + 'b');
            // Clear the file input
            event.target.value = null;
        } else {
            const reader = new FileReader();

            reader.onload = (e) => {
                fileUrl.value = e.target.result;
            };

            form.file = file;

            // Start reading the file
            reader.readAsDataURL(file);
        }
    }

    const submitForm = () => {
        if(!props.contact){
            form.post('/contacts');
        } else {
            form.post('/contacts/' + props.contact.uuid);
        }
    }

    const transformOptions = (optionsString) => {
        return optionsString.split(", ").map(option => ({ label: option, value: option }));
    };
</script>
<template>
    <div class="h-20 bg-white border-b border-1 md:flex items-center justify-between px-10 hidden">
        <div>
            <h1 v-if="!props.contact" class="text-xl">{{ $t('Add contact') }}</h1>
            <h1 v-else class="text-xl">{{ $t('Edit contact') }}</h1>
        </div>
        <div>
            <Link v-if="!props.contact" href="/contacts" class="inline-flex justify-center rounded-md border border-transparent bg-slate-200 px-4 py-2 text-sm text-slate-500 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 mr-4">{{ $t('Cancel') }}</Link>
            <Link v-else :href="'/contacts/' + props.contact.uuid" class="inline-flex justify-center rounded-md border border-transparent bg-slate-200 px-4 py-2 text-sm text-slate-500 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 mr-4">{{ $t('Back') }}</Link>
        </div>
    </div>
    <div class="flex justify-center md:h-[90vh] md:overflow-y-scroll">
        <form @submit.prevent="submitForm()" class="w-[30em]">
            <div class="flex justify-center items-center">
                <div class="rounded-full w-40 h-40 m-4">
                    <svg v-if="fileUrl === null" class="text-gray-500" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M18.685 19.097A9.723 9.723 0 0021.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 003.065 7.097A9.716 9.716 0 0012 21.75a9.716 9.716 0 006.685-2.653zm-12.54-1.285A7.486 7.486 0 0112 15a7.486 7.486 0 015.855 2.812A8.224 8.224 0 0112 20.25a8.224 8.224 0 01-5.855-2.438zM15.75 9a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" clip-rule="evenodd" />
                    </svg>
                    <img v-else class="w-40 h-40 rounded-full object-cover" :src="fileUrl" alt="Contact Image">
                </div>
                <input type="file" class="sr-only" :accept="'.jpg, .png'" id="file-upload" @change="handleFileUpload">
                <label for="file-upload" class="cursor-pointer inline-flex justify-center rounded-md border border-transparent bg-slate-200 px-4 py-2 text-sm text-slate-700 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 mr-4">{{ $t('Upload image') }}</label>
            </div>
            <div class="grid gap-x-6 gap-y-4 sm:grid-cols-6 pb-6 border-b">
                <FormInput v-model="form.first_name" :name="$t('First name')" :error="form.errors.first_name" :type="'text'" :class="'sm:col-span-3'"/>
                <FormInput v-model="form.last_name" :name="$t('Last name')" :error="form.errors.last_name" :type="'text'" :class="'sm:col-span-3'"/>
                <FormPhoneInput v-model="form.phone" :name="$t('Phone')" :error="form.errors.phone" :type="'text'" :class="'sm:col-span-3'"/>
                <FormInput v-model="form.email" :name="$t('Email')" :error="form.errors.email" :type="'text'" :class="'sm:col-span-3'"/>
                <FormSelect v-model="form.group" :name="$t('Group')" :error="form.errors.group" :options="contactGroupOptions()" :type="'text'" :class="'sm:col-span-6'" :multiple="true"/>
            </div>
            <div v-if="locationSettings === 'before' && props.fields.length > 0">
                <div class="grid gap-x-6 gap-y-4 sm:grid-cols-2 mt-4 pb-6 border-b">
                    <div v-for="(input, index) in props.fields" :key="index" :class="input.type != 'input' ? 'sm:col-span-2' : 'sm:col-span-1'">
                        <FormInput v-if="input.type === 'input'" v-model="form.metadata[input.name]" :name="input.name" :label="$t(input.name)" :type="input.value" :class="'sm:col-span-2'" :required="input.required === 1 ? true : false"/>
                        <FormTextArea v-if="input.type === 'textarea'" v-model="form.metadata[input.name]" :name="input.name" :label="$t(input.name)" :class="'sm:col-span-2'" :required="input.required === 1 ? true : false"/>
                        <FormSelect v-if="input.type === 'select'" v-model="form.metadata[input.name]" :name="input.name" :options="transformOptions(input.value)" :class="'sm:col-span-2'" :required="input.required === 1 ? true : false"/>
                        <FormCheckbox v-if="input.type === 'checkbox'" v-model="form.metadata[input.name]" :name="input.name" :label="input.name" :class="'sm:col-span-2'" :required="input.required === 1 ? true : false"/>
                    </div>
                </div>
            </div>
            <div class="grid gap-x-6 gap-y-4 sm:grid-cols-6 pt-4 pb-6">
                <FormInput v-model="form.street" :name="$t('Street')" :error="form.errors.street" :type="'text'" :class="'sm:col-span-6'"/>
                <FormInput v-model="form.city" :name="$t('City')" :error="form.errors.city" :type="'text'" :class="'sm:col-span-3'"/>
                <FormInput v-model="form.state" :name="$t('State')" :error="form.errors.state" :type="'text'" :class="'sm:col-span-3'"/>
                <FormInput v-model="form.zip" :name="$t('Zip code')" :error="form.errors.zip" :type="'text'" :class="'sm:col-span-3'"/>
                <FormInput v-model="form.country" :name="$t('Country')" :error="form.errors.country" :type="'text'" :class="'sm:col-span-3'"/>
            </div>
            <div v-if="locationSettings === 'after' && props.fields.length > 0">
                <div class="grid gap-x-6 gap-y-4 sm:grid-cols-2 mb-8 pt-4 border-t">
                    <div v-for="(input, index) in props.fields" :key="index" :class="input.type != 'input' ? 'sm:col-span-2' : 'sm:col-span-1'">
                        <FormInput v-if="input.type === 'input'" v-model="form.metadata[input.name]" :name="input.name" :label="$t(input.name)" :type="input.value" :class="'sm:col-span-2'" :required="input.required === 1 ? true : false"/>
                        <FormTextArea v-if="input.type === 'textarea'" v-model="form.metadata[input.name]" :name="input.name" :label="$t(input.name)" :class="'sm:col-span-2'" :required="input.required === 1 ? true : false"/>
                        <FormSelect v-if="input.type === 'select'" v-model="form.metadata[input.name]" :name="input.name" :options="transformOptions(input.value)" :class="'sm:col-span-2'" :required="input.required === 1 ? true : false"/>
                        <FormCheckbox v-if="input.type === 'checkbox'" v-model="form.metadata[input.name]" :name="input.name" :label="input.name" :class="'sm:col-span-2'" :required="input.required === 1 ? true : false"/>
                    </div>
                </div>
            </div>
            <div class="mt-4 mb-10 pb-10 flex">
                <Link href="/contacts" class="inline-flex justify-center rounded-md border border-transparent bg-slate-50 px-4 py-2 text-sm text-slate-500 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 mr-4">{{ $t('Cancel') }}</Link>
                <button :class="'inline-flex justify-center rounded-md border border-transparent bg-primary px-4 py-2 text-sm text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2'">
                    <span>{{ $t('Save') }}</span>
                </button>
            </div>
        </form>
    </div>
</template> -->


<!-- ========================================== NEW UI CODE ==================================== -->

<script setup>
import { ref } from 'vue';
import { Link, useForm } from "@inertiajs/vue3";
import FormCheckbox from '@/Components/FormCheckbox.vue';
import FormInput from '@/Components/FormInput.vue';
import FormPhoneInput from '@/Components/FormPhoneInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import FormTextArea from '@/Components/FormTextArea.vue';
import { trans } from 'laravel-vue-i18n';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps(['contactGroups', 'contact', 'fields', 'locationSettings']);
const fileUrl = props.contact?.avatar ? ref(props.contact?.avatar) : ref(null);
const inputFields = props.fields;

const defaultFields = inputFields.reduce((acc, field) => {
    acc[field.name] = "";
    return acc;
}, {});

const getAddressDetail = (value, key) => {
    const address = JSON.parse(value);
    return address?.[key] ?? '';
}

const form = useForm({
    first_name: props.contact?.first_name ?? null,
    last_name: props.contact?.last_name ?? null,
    phone: props.contact?.phone ?? null,
    email: props.contact?.email ?? null,
    group: props.contact?.contact_groups ? props.contact.contact_groups.map(group => group.uuid || group) : [],
    file: null,
    street: props.contact?.address ? getAddressDetail(props.contact?.address, 'street') : null,
    city: props.contact?.address ? getAddressDetail(props.contact?.address, 'city') : null,
    state: props.contact?.address ? getAddressDetail(props.contact?.address, 'state') : null,
    zip: props.contact?.address ? getAddressDetail(props.contact?.address, 'zip') : null,
    country: props.contact?.address ? getAddressDetail(props.contact?.address, 'country') : null,
    metadata: props.contact?.metadata ? JSON.parse(props.contact?.metadata) : defaultFields,
});

const contactGroupOptions = () => {
    return props.contactGroups.map((option) => ({
        value: option.uuid,
        label: option.name,
    }));
};

const handleFileUpload = (event) => {
    const fileSizeLimit = 5 * 1024 * 1024;
    const file = event.target.files[0];

    if (file && file.size > fileSizeLimit) {
        alert(trans('file size exceeds the limit. Max allowed size:') + ' ' + fileSizeLimit + 'b');
        event.target.value = null;
    } else {
        const reader = new FileReader();
        reader.onload = (e) => {
            fileUrl.value = e.target.result;
        };
        form.file = file;
        reader.readAsDataURL(file);
    }
}

const submitForm = () => {
    if (!props.contact) {
        form.post('/contacts');
    } else {
        form.post('/contacts/' + props.contact.uuid);
    }
}

const transformOptions = (optionsString) => {
    return optionsString.split(", ").map(option => ({ label: option, value: option }));
};
</script>

<template>
    <!-- Header -->
    <div class="hidden md:flex items-center justify-between px-8 py-6 bg-white border-b border-gray-100">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ props.contact ? $t('Edit contact') : $t('Add contact') }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">{{ props.contact ? $t(`Update contact information`) : $t(`Create a new
                contact`) }}</p>
        </div>
        <div class="flex gap-3">
            <Link v-if="!props.contact" href="/contacts"
                class="px-6 py-2.5 border-2 border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all">
            {{ $t('Cancel') }}
            </Link>
            <Link v-else :href="'/contacts/' + props.contact.uuid"
                class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-all duration-200 border border-slate-300 flex items-center space-x-2">
            <ArrowLeft class="w-4 h-4" />
            <span>{{ $t('Back') }}</span>
            </Link>
        </div>
    </div>

    <!-- Form Content -->
    <div class="md:h-[calc(100vh-96px)] md:overflow-y-auto bg-gradient-to-br from-gray-50 to-orange-50/30">
        <div class="max-w-3xl mx-auto p-8">
            <form @submit.prevent="submitForm()" class="space-y-8">

                <!-- Avatar Section -->
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <div class="flex flex-col items-center">
                        <div class="relative group">
                            <div class="w-32 h-32 rounded-full overflow-hidden ring-4 ring-[#ff5100]/20">
                                <svg v-if="fileUrl === null" class="w-full h-full text-gray-300 bg-gray-100"
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M18.685 19.097A9.723 9.723 0 0021.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 003.065 7.097A9.716 9.716 0 0012 21.75a9.716 9.716 0 006.685-2.653zm-12.54-1.285A7.486 7.486 0 0112 15a7.486 7.486 0 015.855 2.812A8.224 8.224 0 0112 20.25a8.224 8.224 0 01-5.855-2.438zM15.75 9a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                <img v-else class="w-full h-full object-cover" :src="fileUrl" alt="Contact Image">
                            </div>
                            <div
                                class="absolute inset-0 rounded-full bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                    class="text-white">
                                    <path fill="currentColor"
                                        d="M12 12.5a3.5 3.5 0 1 0 0 7a3.5 3.5 0 0 0 0-7ZM10.5 16a1.5 1.5 0 1 1 3 0a1.5 1.5 0 0 1-3 0Z" />
                                    <path fill="currentColor"
                                        d="M9.778 4.002C10.6 3.368 11.268 3 12 3c.732 0 1.4.368 2.222 1.002c.819.632 1.74 1.596 2.787 2.816l.232.27c.622.727 1.093 1.276 1.526 1.644c.446.378.756.5.982.54c.524.094 1.093.158 1.814.235l.28.03c1.328.141 2.395.255 3.19.532a3.003 3.003 0 0 1 1.72 3.688c-.259.957-.867 1.804-1.668 2.887c-.784 1.06-1.802 2.324-3.044 3.882l-.13.162c-.505.636-.892 1.121-1.254 1.47c-.376.363-.656.523-.934.596a3.003 3.003 0 0 1-2.594-.466c-.257-.191-.493-.498-.818-.926c-.313-.412-.688-.946-1.148-1.602l-.232-.331c-.622-.888-1.093-1.56-1.526-2.04c-.446-.494-.756-.681-.982-.751a3.003 3.003 0 0 0-1.814-.313l-.28.024c-1.328.113-2.395.203-3.19.117a3.003 3.003 0 0 1-2.493-2.96c-.02-.992.27-1.95.815-3.143c.533-1.167 1.28-2.557 2.25-4.256l.103-.18c.394-.69.695-1.216.994-1.614c.31-.413.556-.635.821-.775a3.003 3.003 0 0 1 2.594 0c.265.14.51.362.821.775c.3.398.6.924.994 1.614l.103.18Z" />
                                </svg>
                            </div>
                        </div>
                        <input type="file" class="sr-only" accept=".jpg,.png" id="file-upload"
                            @change="handleFileUpload">
                        <label for="file-upload"
                            class="mt-6 cursor-pointer inline-flex items-center gap-2 px-6 py-2.5 bg-[#ff5100] hover:bg-[#e64900] text-white rounded-xl font-medium transition-all shadow-md hover:shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M11 16V7.85l-2.6 2.6L7 9l5-5l5 5l-1.4 1.45l-2.6-2.6V16h-2Zm-5 4q-.825 0-1.413-.588T4 18v-3h2v3h12v-3h2v3q0 .825-.588 1.413T18 20H6Z" />
                            </svg>
                            {{ $t('Upload image') }}
                        </label>
                        <p class="text-xs text-gray-500 mt-2">{{ $t('JPG or PNG. Max 5MB') }}</p>
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-[#ff5100]/10 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                class="text-[#ff5100]">
                                <path fill="currentColor"
                                    d="M12 12q-1.65 0-2.825-1.175T8 8q0-1.65 1.175-2.825T12 4q1.65 0 2.825 1.175T16 8q0 1.65-1.175 2.825T12 12Zm-8 8v-2.8q0-.85.438-1.563T5.6 14.55q1.55-.775 3.15-1.163T12 13q1.65 0 3.25.388t3.15 1.162q.725.375 1.163 1.088T20 17.2V20H4Z" />
                            </svg>
                        </div>
                        {{ $t('Basic Information') }}
                    </h2>
                    <div class="grid gap-6 sm:grid-cols-2">
                        <FormInput v-model="form.first_name" :name="$t('First name')" :error="form.errors.first_name"
                            :type="'text'" :class="'sm:col-span-1'" />
                        <FormInput v-model="form.last_name" :name="$t('Last name')" :error="form.errors.last_name"
                            :type="'text'" :class="'sm:col-span-1'" />
                        <FormPhoneInput v-model="form.phone" :name="$t('Phone')" :error="form.errors.phone"
                            :type="'text'" :class="'sm:col-span-1'" />
                        <FormInput v-model="form.email" :name="$t('Email')" :error="form.errors.email" :type="'text'"
                            :class="'sm:col-span-1'" />
                        <FormSelect v-model="form.group" :name="$t('Group')" :error="form.errors.group"
                            :options="contactGroupOptions()" :type="'text'" :class="'sm:col-span-2'" :multiple="true" />
                    </div>
                </div>

                <!-- Custom Fields (Before Location) -->
                <div v-if="locationSettings === 'before' && props.fields.length > 0"
                    class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-[#ff5100]/10 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                class="text-[#ff5100]">
                                <path fill="currentColor"
                                    d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8s8 3.59 8 8s-3.59 8-8 8m-5-9h10v2H7z" />
                            </svg>
                        </div>
                        {{ $t('Additional Information') }}
                    </h2>
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div v-for="(input, index) in props.fields" :key="index"
                            :class="input.type != 'input' ? 'sm:col-span-2' : 'sm:col-span-1'">
                            <FormInput v-if="input.type === 'input'" v-model="form.metadata[input.name]"
                                :name="input.name" :label="$t(input.name)" :type="input.value"
                                :required="input.required === 1" />
                            <FormTextArea v-if="input.type === 'textarea'" v-model="form.metadata[input.name]"
                                :name="input.name" :label="$t(input.name)" :required="input.required === 1" />
                            <FormSelect v-if="input.type === 'select'" v-model="form.metadata[input.name]"
                                :name="input.name" :options="transformOptions(input.value)"
                                :required="input.required === 1" />
                            <FormCheckbox v-if="input.type === 'checkbox'" v-model="form.metadata[input.name]"
                                :name="input.name" :label="input.name" :required="input.required === 1" />
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-[#ff5100]/10 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                class="text-[#ff5100]">
                                <path fill="currentColor"
                                    d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5s2.5 1.12 2.5 2.5s-1.12 2.5-2.5 2.5z" />
                            </svg>
                        </div>
                        {{ $t('Address Information') }}
                    </h2>
                    <div class="grid gap-6 sm:grid-cols-2">
                        <FormInput v-model="form.street" :name="$t('Street')" :error="form.errors.street" :type="'text'"
                            :class="'sm:col-span-2'" />
                        <FormInput v-model="form.city" :name="$t('City')" :error="form.errors.city" :type="'text'" />
                        <FormInput v-model="form.state" :name="$t('State')" :error="form.errors.state" :type="'text'" />
                        <FormInput v-model="form.zip" :name="$t('Zip code')" :error="form.errors.zip" :type="'text'" />
                        <FormInput v-model="form.country" :name="$t('Country')" :error="form.errors.country"
                            :type="'text'" />
                    </div>
                </div>

                <!-- Custom Fields (After Location) -->
                <div v-if="locationSettings === 'after' && props.fields.length > 0"
                    class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-[#ff5100]/10 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                class="text-[#ff5100]">
                                <path fill="currentColor"
                                    d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8s8 3.59 8 8s-3.59 8-8 8m-5-9h10v2H7z" />
                            </svg>
                        </div>
                        {{ $t('Additional Information') }}
                    </h2>
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div v-for="(input, index) in props.fields" :key="index"
                            :class="input.type != 'input' ? 'sm:col-span-2' : 'sm:col-span-1'">
                            <FormInput v-if="input.type === 'input'" v-model="form.metadata[input.name]"
                                :name="input.name" :label="$t(input.name)" :type="input.value"
                                :required="input.required === 1" />
                            <FormTextArea v-if="input.type === 'textarea'" v-model="form.metadata[input.name]"
                                :name="input.name" :label="$t(input.name)" :required="input.required === 1" />
                            <FormSelect v-if="input.type === 'select'" v-model="form.metadata[input.name]"
                                :name="input.name" :options="transformOptions(input.value)"
                                :required="input.required === 1" />
                            <FormCheckbox v-if="input.type === 'checkbox'" v-model="form.metadata[input.name]"
                                :name="input.name" :label="input.name" :required="input.required === 1" />
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex pb-16 gap-4 sticky bottom-4 bg-gradient-to-br from-gray-50 to-orange-50/30 py-6 -mx-8 px-8">
                    <Link href="/contacts"
                        class="flex-1 sm:flex-none px-8 py-3.5 border-2 border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-white transition-all text-center">
                    {{ $t('Cancel') }}
                    </Link>
                    <button type="submit"
                        class="flex-1 sm:flex-none px-8 py-3.5 bg-[#ff5100] hover:bg-[#e64900] text-white rounded-xl font-medium transition-all shadow-lg hover:shadow-xl">
                        {{ $t('Save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
