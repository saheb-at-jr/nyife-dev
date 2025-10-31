<!-- <script setup>
import { useForm } from "@inertiajs/vue3";
import { defineProps, ref } from "vue";
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import FormInput from '@/Components/FormInput.vue';
import FormPhoneInput from '@/Components/FormPhoneInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import { usePage, router } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    organization: Object,
    role: String,
    isOpen: Boolean,
})

const isLoading = ref(false);

const form1 = useForm({
    first_name: props.user.first_name,
    last_name: props.user.last_name,
    email: props.user.email,
    phone: props.user.phone,
})

const form3 = useForm({
    old_password: null,
    password: null,
    password_confirmation: null
})

const form4 = useForm({
    status: usePage().props.tfa.enabled,
    token: null,
    secret: usePage().props.tfa.secret,
});

const tfa = usePage().props.tfa;

const submitForm = async (event) => {
    isLoading.value = true;

    form1.put('/profile/', {
        preserveScroll: true,
        onFinish: () => {
            isLoading.value = false;
        }
    });
};

const submitForm3 = async (event) => {
    isLoading.value = true;

    form3.put('/profile/password', {
        preserveScroll: true,
        onFinish: () => {
            isLoading.value = false;
        }
    });
};

const submitForm4 = async (event) => {
    isLoading.value = true;

    form4.put('/profile/tfa', {
        preserveScroll: true,
        onFinish: () => {
            isLoading.value = false;
        },
        onSuccess: () => {
            router.visit(router.page.url);
        },
    });
};

const emit = defineEmits(['close']);

function closeModal() {
    emit('close', true);
}

</script>

<template>
    <TransitionRoot appear :show="isOpen" as="template">
        <Dialog as="div" @close="closeModal" class="relative z-10">
            <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100"
                leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-black bg-opacity-25" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100" leave="duration-200 ease-in" leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95">
                        <DialogPanel
                            class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all min-w-[20em]">
                            <div>
                                <h2 class="text-base leading-7 text-gray-900">{{ $t('Personal information') }}
                                </h2>
                                <p class="mb-4 text-sm leading-6 text-gray-600">{{ $t(`Update your account details and
                                    credentials`) }}</p>

                                <TabGroup>
                                    <TabList class="flex space-x-1 rounded-xl bg-primary p-1">
                                        <Tab as="template" v-slot="{ selected }">
                                            <button :class="[
                                                'w-full rounded-lg py-2.5 text-sm leading-5 text-[#ffffffcc]',
                                                'ring-white focus:outline-none',
                                                selected
                                                    ? 'bg-white text-black shadow'
                                                    : 'hover:bg-white hover:text-black',
                                            ]">
                                                {{ $t('My profile') }}
                                            </button>
                                        </Tab>
                                        <Tab as="template" v-slot="{ selected }">
                                            <button :class="[
                                                'w-full rounded-lg py-2.5 text-sm leading-5 text-[#ffffffcc]',
                                                'ring-white focus:outline-none',
                                                selected
                                                    ? 'bg-white text-black shadow'
                                                    : 'hover:bg-white hover:text-black',
                                            ]">
                                                {{ $t('Security') }}
                                            </button>
                                        </Tab>
                                        <Tab v-if="tfa.status" as="template" v-slot="{ selected }">
                                            <button :class="[
                                                'w-full rounded-lg py-2.5 text-sm leading-5 text-[#ffffffcc]',
                                                'ring-white focus:outline-none',
                                                selected
                                                    ? 'bg-white text-black shadow'
                                                    : 'hover:bg-white hover:text-black',
                                            ]">
                                                {{ $t('Two-Factor') }}
                                            </button>
                                        </Tab>
                                    </TabList>

                                    <TabPanels class="mt-2">
                                        <TabPanel>
                                            <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-4">
                                                <form @submit.prevent="submitForm()"
                                                    class="grid gap-x-6 gap-y-4 sm:grid-cols-6">
                                                    <FormInput v-model="form1.first_name" :name="$t('First name')"
                                                        :error="form1.errors.first_name" :type="'text'"
                                                        :class="'sm:col-span-3'" />
                                                    <FormInput v-model="form1.last_name" :name="$t('Last name')"
                                                        :error="form1.errors.last_name" :type="'text'"
                                                        :class="'sm:col-span-3'" />
                                                    <FormInput v-model="form1.email" :name="$t('Email')"
                                                        :error="form1.errors.email" :type="'text'"
                                                        :class="'sm:col-span-6'" />
                                                    <FormPhoneInput v-model="form1.phone" :name="$t('Phone')"
                                                        :error="form1.errors.phone" :type="'text'"
                                                        :class="'sm:col-span-6'" />

                                                    <div class="mt-4 flex">
                                                        <button type="button" @click="closeModal"
                                                            class="inline-flex justify-center rounded-md border border-transparent bg-slate-50 px-4 py-2 text-sm text-slate-500 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 mr-4">{{
                                                                $t('Cancel') }}</button>
                                                        <button
                                                            :class="['inline-flex justify-center rounded-md border border-transparent bg-primary px-4 py-2 text-sm text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2', { 'opacity-50': isLoading }]"
                                                            :disabled="isLoading">
                                                            <svg v-if="isLoading" xmlns="http://www.w3.org/2000/svg"
                                                                width="20" height="20" viewBox="0 0 24 24">
                                                                <path fill="currentColor"
                                                                    d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                                                    opacity=".5" />
                                                                <path fill="currentColor"
                                                                    d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                                                                    <animateTransform attributeName="transform" dur="1s"
                                                                        from="0 12 12" repeatCount="indefinite"
                                                                        to="360 12 12" type="rotate" />
                                                                </path>
                                                            </svg>
                                                            <span v-else>{{ $t('Save') }}</span>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </TabPanel>
                                        <TabPanel>
                                            <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-4">
                                                <form @submit.prevent="submitForm3()"
                                                    class="grid gap-x-6 gap-y-4 sm:grid-cols-6">
                                                    <FormInput v-model="form3.old_password" :name="$t('Old password')"
                                                        :error="form3.errors.old_password" :type="'password'"
                                                        :class="'sm:col-span-6'" />
                                                    <FormInput v-model="form3.password" :name="$t('New password')"
                                                        :error="form3.errors.password" :type="'password'"
                                                        :class="'sm:col-span-6'" />
                                                    <FormInput v-model="form3.password_confirmation"
                                                        :name="$t('Confirm password')"
                                                        :error="form3.errors.password_confirmation" :type="'password'"
                                                        :class="'sm:col-span-6'" />

                                                    <div class="mt-4 flex">
                                                        <button type="button" @click="closeModal"
                                                            class="inline-flex justify-center rounded-md border border-transparent bg-slate-50 px-4 py-2 text-sm text-slate-500 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 mr-4">{{
                                                                $t('Cancel') }}</button>
                                                        <button
                                                            :class="['inline-flex justify-center rounded-md border border-transparent bg-primary px-4 py-2 text-sm text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2', { 'opacity-50': isLoading }]"
                                                            :disabled="isLoading">
                                                            <svg v-if="isLoading" xmlns="http://www.w3.org/2000/svg"
                                                                width="20" height="20" viewBox="0 0 24 24">
                                                                <path fill="currentColor"
                                                                    d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                                                    opacity=".5" />
                                                                <path fill="currentColor"
                                                                    d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                                                                    <animateTransform attributeName="transform" dur="1s"
                                                                        from="0 12 12" repeatCount="indefinite"
                                                                        to="360 12 12" type="rotate" />
                                                                </path>
                                                            </svg>
                                                            <span v-else>{{ $t('Save') }}</span>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </TabPanel>
                                        <TabPanel v-if="tfa.status">
                                            <form @submit.prevent="submitForm4()">
                                                <FormSelect name="Two-factor authentication" v-model="form4.status"
                                                    :options="[
                                                        { value: 1, label: 'Enable' },
                                                        { value: 0, label: 'Disable' },
                                                    ]" :error="form4.errors.status" />

                                                <div v-if="tfa.enabled === 1" class="mt-2 text-sm text-green-700">
                                                    Two-factor authentication is <b>enabled</b>.
                                                </div>

                                                <div class="relative mt-4">
                                                    <div class="absolute inset-0 backdrop-blur-sm"
                                                        v-if="form4.status === 0" />
                                                    <h3 class="font-semibold">1. Scan QR Code</h3>
                                                    <p class="text-gray-600 text-sm">
                                                        Open the authentication app (ex: Authy, Google
                                                        Authenticator) on your mobile device and scan the
                                                        following QR Code with your camera.
                                                    </p>
                                                    <div class="flex items-center text-sm">
                                                        <div class="mb-6 md:mb-0 md:mr-2">
                                                            <img :src="tfa.qrcode" alt="qrcode" width="200" />
                                                        </div>
                                                        <div>
                                                            <label>Can't scan the QR Code?</label>
                                                            <p class="text-gray-600 text-sm">
                                                                Try inserting the following secret code into your app if
                                                                you can't scan the QR Code.
                                                            </p>
                                                            <p class="h5 text-sm mt-3">{{ tfa.secret }}</p>
                                                        </div>
                                                    </div>
                                                    <h3 class="font-semibold">
                                                        2. Enter Token from Authenticator App
                                                    </h3>
                                                    <p class="text-sm text-gray-600 mb-2">
                                                        To confirm that you setup your code properly, please
                                                        enter the 6-digit token from your mobile app.
                                                    </p>
                                                    <FormInput v-model="form4.token" placeholder="6 digit code"
                                                        :error="form4.errors.token" :type="'text'"
                                                        :class="'sm:col-span-6'" />
                                                </div>
                                                <div class="mt-4 flex">
                                                    <button type="button" @click="closeModal"
                                                        class="inline-flex justify-center rounded-md border border-transparent bg-slate-50 px-4 py-2 text-sm text-slate-500 hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 mr-4">{{
                                                            $t('Cancel') }}</button>
                                                    <button
                                                        :class="['inline-flex justify-center rounded-md border border-transparent bg-primary px-4 py-2 text-sm text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2', { 'opacity-50': isLoading }]"
                                                        :disabled="isLoading">
                                                        <svg v-if="isLoading" xmlns="http://www.w3.org/2000/svg"
                                                            width="20" height="20" viewBox="0 0 24 24">
                                                            <path fill="currentColor"
                                                                d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                                                opacity=".5" />
                                                            <path fill="currentColor"
                                                                d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                                                                <animateTransform attributeName="transform" dur="1s"
                                                                    from="0 12 12" repeatCount="indefinite"
                                                                    to="360 12 12" type="rotate" />
                                                            </path>
                                                        </svg>
                                                        <span v-else>{{ $t('Save') }}</span>
                                                    </button>
                                                </div>
                                            </form>
                                        </TabPanel>
                                    </TabPanels>
                                </TabGroup>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template> -->

<!-- =============================== NEW CODE =============================== -->

<script setup>
import { useForm } from "@inertiajs/vue3";
import { defineProps, ref, computed } from 'vue';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import FormInput from '@/Components/FormInput.vue';
import FormPhoneInput from '@/Components/FormPhoneInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import { usePage, router } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    organization: Object,
    role: String,
    isOpen: Boolean,
})

const isLoading = ref(false);

const form1 = useForm({
    first_name: props.user.first_name,
    last_name: props.user.last_name,
    email: props.user.email,
    phone: props.user.phone,
})

const form3 = useForm({
    old_password: null,
    password: null,
    password_confirmation: null
})

const form4 = useForm({
    status: usePage().props.tfa.enabled,
    token: null,
    secret: usePage().props.tfa.secret,
});

const tfa = usePage().props.tfa;

const submitForm = async () => {
    isLoading.value = true;
    form1.put('/profile/', {
        preserveScroll: true,
        onFinish: () => { isLoading.value = false; }
    });
};

const submitForm3 = async () => {
    isLoading.value = true;
    form3.put('/profile/password', {
        preserveScroll: true,
        onFinish: () => { isLoading.value = false; }
    });
};

const submitForm4 = async () => {
    isLoading.value = true;
    form4.put('/profile/tfa', {
        preserveScroll: true,
        onFinish: () => { isLoading.value = false; },
        onSuccess: () => { router.visit(router.page.url); }
    });
};

const emit = defineEmits(['close']);
function closeModal() { emit('close', true); }

const initials = computed(() => {
    const f = props.user.first_name || '';
    const l = props.user.last_name || '';
    return (f.charAt(0) + (l.charAt(0) || '')).toUpperCase();
});
</script>

<template>
    <TransitionRoot appear :show="isOpen" as="template">
        <Dialog as="div" @close="closeModal" class="relative z-50">

            <!-- backdrop -->
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gradient-to-b from-black/40 via-black/30 to-black/30 backdrop-blur-sm" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100" leave="duration-200 ease-in" leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95">

                        <DialogPanel
                            class="w-full max-w-3xl transform overflow-hidden rounded-2xl bg-white/95 shadow-2xl ring-1 ring-slate-900/5 transition-all">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6 md:p-8">

                                <!-- Left column: user summary & actions -->
                                <aside
                                    class="col-span-1 rounded-xl bg-gradient-to-br from-slate-50 to-white p-4 md:p-6 flex flex-col gap-4">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="h-16 w-16 rounded-full bg-primary-600/10 flex items-center justify-center text-xl font-semibold text-primary-600 ring-1 ring-primary-100">
                                            {{ initials }}
                                        </div>
                                        <div class="flex-grow">
                                            <h3 class="text-lg font-semibold text-slate-900">{{ user.first_name }} {{
                                                user.last_name }}</h3>
                                            <p class="text-start text-sm text-slate-500">{{ role || organization?.name
                                            }}</p>
                                        </div>
                                    </div>

                                    <div class="space-y-2 text-sm text-slate-600">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-start text-xs text-slate-400">Email</p>
                                                <p class="truncate">{{ user.email }}</p>
                                            </div>
                                        </div>
                                        <div class="text-start flex items-center justify-between">
                                            <div>
                                                <p class="text-xs text-slate-400">Phone</p>
                                                <p class="truncate">{{ user.phone || '-' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </aside>

                                <!-- Right column: tabs and forms -->
                                <div class="col-span-2 bg-white p-2 md:p-6">
                                    <header class="flex items-start justify-between gap-4">
                                        <div>
                                            <h2 class="text-start text-lg font-semibold text-slate-900">{{ $t(`Personal
                                                information`) }}</h2>
                                            <p class="mt-1 text-sm text-slate-500">{{ $t(`Update your account details
                                                and credentials`) }}</p>
                                        </div>
                                        <button @click="closeModal"
                                            class="ml-auto rounded-md p-2 text-slate-500 hover:bg-slate-100/60 focus:outline-none"><svg
                                                xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg></button>
                                    </header>

                                    <div class="mt-4">
                                        <TabGroup as="div">
                                            <TabList class="flex w-full gap-2 rounded-lg bg-primary p-1 shadow-inner">
                                                <Tab as="template" v-slot="{ selected }">
                                                    <button
                                                        :class="['flex-1 py-2 px-3 text-sm rounded-md font-medium transition-all', selected ? 'bg-white text-slate-900 shadow' : 'text-white/90 hover:bg-white/10']">{{
                                                            $t('My profile') }}</button>
                                                </Tab>
                                                <Tab as="template" v-slot="{ selected }">
                                                    <button
                                                        :class="['flex-1 py-2 px-3 text-sm rounded-md font-medium transition-all', selected ? 'bg-white text-slate-900 shadow' : 'text-white/90 hover:bg-white/10']">{{
                                                            $t('Security') }}</button>
                                                </Tab>
                                                <Tab v-if="tfa.status" as="template" v-slot="{ selected }">
                                                    <button
                                                        :class="['flex-1 py-2 px-3 text-sm rounded-md font-medium transition-all', selected ? 'bg-white text-slate-900 shadow' : 'text-white/90 hover:bg-white/10']">{{
                                                            $t('Two-Factor') }}</button>
                                                </Tab>
                                            </TabList>

                                            <TabPanels class="mt-4">
                                                <!-- PROFILE -->
                                                <TabPanel>
                                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 text-start">
                                                        <form @submit.prevent="submitForm()"
                                                            class="sm:col-span-2 grid gap-4">
                                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                                <FormInput v-model="form1.first_name"
                                                                    :name="$t('First name')"
                                                                    :error="form1.errors.first_name" :type="'text'" />
                                                                <FormInput v-model="form1.last_name"
                                                                    :name="$t('Last name')"
                                                                    :error="form1.errors.last_name" :type="'text'" />
                                                            </div>

                                                            <FormInput v-model="form1.email" :name="$t('Email')"
                                                                :error="form1.errors.email" :type="'text'" />
                                                            <FormPhoneInput v-model="form1.phone" :name="$t('Phone')"
                                                                :error="form1.errors.phone" :type="'text'" />

                                                            <div class="flex items-center justify-end gap-3">
                                                                <button type="button" @click="closeModal"
                                                                    class="rounded-md px-4 py-2 text-sm font-medium bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">{{
                                                                        $t('Cancel') }}</button>
                                                                <button
                                                                    :class="['rounded-md px-4 py-2 text-sm font-medium text-white', { 'bg-primary opacity-60': isLoading, 'bg-primary': !isLoading }]"
                                                                    :disabled="isLoading">
                                                                    <svg v-if="isLoading"
                                                                        xmlns="http://www.w3.org/2000/svg" width="18"
                                                                        height="18" viewBox="0 0 24 24"
                                                                        class="inline-block animate-spin">
                                                                        <path fill="currentColor"
                                                                            d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                                                            opacity=".4" />
                                                                        <path fill="currentColor"
                                                                            d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z" />
                                                                    </svg>
                                                                    <span v-else>{{ $t('Save') }}</span>
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </TabPanel>

                                                <!-- SECURITY -->
                                                <TabPanel>
                                                    <div class="grid grid-cols-1 gap-4 text-start">
                                                        <form @submit.prevent="submitForm3()" class="grid gap-4">
                                                            <FormInput v-model="form3.old_password"
                                                                :name="$t('Old password')"
                                                                :error="form3.errors.old_password" :type="'password'" />
                                                            <FormInput v-model="form3.password"
                                                                :name="$t('New password')"
                                                                :error="form3.errors.password" :type="'password'" />
                                                            <FormInput v-model="form3.password_confirmation"
                                                                :name="$t('Confirm password')"
                                                                :error="form3.errors.password_confirmation"
                                                                :type="'password'" />

                                                            <div class="flex items-center justify-end gap-3">
                                                                <button type="button" @click="closeModal"
                                                                    class="rounded-md px-4 py-2 text-sm font-medium bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">{{
                                                                        $t('Cancel') }}</button>
                                                                <button
                                                                    :class="['rounded-md px-4 py-2 text-sm font-medium text-white', { 'bg-primary opacity-60': isLoading, 'bg-primary': !isLoading }]"
                                                                    :disabled="isLoading">
                                                                    <svg v-if="isLoading"
                                                                        xmlns="http://www.w3.org/2000/svg" width="18"
                                                                        height="18" viewBox="0 0 24 24"
                                                                        class="inline-block animate-spin">
                                                                        <path fill="currentColor"
                                                                            d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                                                            opacity=".4" />
                                                                        <path fill="currentColor"
                                                                            d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z" />
                                                                    </svg>
                                                                    <span v-else>{{ $t('Save') }}</span>
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </TabPanel>

                                                <!-- TWO-FACTOR -->
                                                <TabPanel v-if="tfa.status">
                                                    <div class="grid grid-cols-1 gap-4">
                                                        <form @submit.prevent="submitForm4()">
                                                            <FormSelect name="Two-factor authentication"
                                                                v-model="form4.status" :options="[
                                                                    { value: 1, label: 'Enable' },
                                                                    { value: 0, label: 'Disable' },
                                                                ]" :error="form4.errors.status" />

                                                            <div v-if="tfa.enabled === 1"
                                                                class="mt-2 text-sm text-green-700">Two-factor
                                                                authentication is <b>enabled</b>.</div>

                                                            <div
                                                                class="relative mt-4 rounded-md border border-slate-100 p-4 bg-slate-50">
                                                                <h3 class="font-semibold text-slate-800">1. Scan QR Code
                                                                </h3>
                                                                <p class="text-sm text-slate-600">Open an authenticator
                                                                    app (Authy, Google Authenticator) and scan the QR
                                                                    code below.</p>

                                                                <div
                                                                    class="flex flex-col md:flex-row gap-4 items-start mt-3">
                                                                    <img :src="tfa.qrcode" alt="qrcode"
                                                                        class="h-40 w-40 rounded-md shadow-sm object-contain" />
                                                                    <div class="text-sm text-slate-600 flex-1">
                                                                        <label
                                                                            class="block text-xs text-slate-400">Can't
                                                                            scan?</label>
                                                                        <p class="mt-2">Insert this secret code into
                                                                            your app instead:</p>
                                                                        <div
                                                                            class="mt-2 inline-flex items-center gap-2 rounded bg-white px-3 py-2 text-xs font-mono text-slate-700 border border-slate-200">
                                                                            {{ tfa.secret }}
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <h3 class="mt-4 font-semibold text-slate-800">2. Enter
                                                                    token</h3>
                                                                <p class="text-sm text-slate-600">Enter the 6-digit
                                                                    token from your authenticator app to confirm setup.
                                                                </p>
                                                                <FormInput v-model="form4.token"
                                                                    placeholder="6 digit code"
                                                                    :error="form4.errors.token" :type="'text'" />
                                                            </div>

                                                            <div class="flex items-center justify-end gap-3 mt-4">
                                                                <button type="button" @click="closeModal"
                                                                    class="rounded-md px-4 py-2 text-sm font-medium bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">{{
                                                                        $t('Cancel') }}</button>
                                                                <button
                                                                    :class="['rounded-md px-4 py-2 text-sm font-medium text-white', { 'bg-primary opacity-60': isLoading, 'bg-primary': !isLoading }]"
                                                                    :disabled="isLoading">
                                                                    <svg v-if="isLoading"
                                                                        xmlns="http://www.w3.org/2000/svg" width="18"
                                                                        height="18" viewBox="0 0 24 24"
                                                                        class="inline-block animate-spin">
                                                                        <path fill="currentColor"
                                                                            d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                                                            opacity=".4" />
                                                                        <path fill="currentColor"
                                                                            d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z" />
                                                                    </svg>
                                                                    <span v-else>{{ $t('Save') }}</span>
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </TabPanel>

                                            </TabPanels>
                                        </TabGroup>
                                    </div>
                                </div>

                            </div>
                        </DialogPanel>

                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>