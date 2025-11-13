<!-- <script setup>
    import { ref, watchEffect } from 'vue';
    import { Link, router } from '@inertiajs/vue3';
    import Dropdown from '@/Components/Dropdown.vue';
    import DropdownItemGroup from '@/Components/DropdownItemGroup.vue';
    import DropdownItem from '@/Components/DropdownItem.vue';
    import { trans } from 'laravel-vue-i18n';
    
    const props = defineProps(['contact', 'fields', 'locationSettings']);
    const contact = ref(props.contact);
    const metadata = (props.contact.metadata !== null && props.contact.metadata !== '') ? JSON.parse(props.contact.metadata) : {};

    watchEffect(() => {
        contact.value = props.contact;
    });

    const favorite = async() => {
        contact.value.is_favorite = !contact.value.is_favorite;

        router.put('/contacts/favorite/' + contact.value.uuid, { favorite: contact.value.is_favorite });
    }

    const deleteRow = async() => {
        router.visit('/contacts', {
            method: 'delete',
            data: { 'uuids': [ contact.value.uuid ]},
            preserveState: true
        })
    }

    const getAddressDetail = (value, key) => {
        const address = JSON.parse(value);
        return address?.[key] && address?.[key] != 'Not Set' ? address?.[key] : trans('not set');
    }
</script>
<template>
    <div class="px-20 overflow-y-scroll h-screen">
        <div class="flex justify-center space-x-8 items-center pb-6 pr-20 border-gray-300 border-b">
            <div>
                <div class="rounded-full p-1">
                    <img v-if="contact.avatar" class="rounded-full w-40 h-40" :src="contact.avatar">
                    <div v-else class="rounded-full w-40 h-40">
                        <svg class="text-gray-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M18.685 19.097A9.723 9.723 0 0021.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 003.065 7.097A9.716 9.716 0 0012 21.75a9.716 9.716 0 006.685-2.653zm-12.54-1.285A7.486 7.486 0 0112 15a7.486 7.486 0 015.855 2.812A8.224 8.224 0 0112 20.25a8.224 8.224 0 01-5.855-2.438zM15.75 9a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>
            <div>
                <h1 class="text-3xl">{{ contact.full_name }}</h1>
                <div class="text-slate-500 truncate flex items-center">
                    <span class="text-sm">{{ contact.formatted_phone_number }}</span>
                </div>
                <div class="flex space-x-3 mt-4">
                    <button class="bg-gray-200 h-9 px-4 rounded-md flex items-center">
                        <Link :href="'/contacts/' + contact.uuid + '?edit=true'" class="text-[14px]">{{ $t('Edit') }}</Link>
                    </button>
                    <button class="bg-gray-200 h-9 px-4 rounded-md flex items-center">
                        <Link :href="'/chats/' + contact.uuid" class="text-[14px]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 256 256"><path fill="currentColor" d="M216 80h-32V48a16 16 0 0 0-16-16H40a16 16 0 0 0-16 16v128a8 8 0 0 0 13 6.22L72 154v30a16 16 0 0 0 16 16h93.59L219 230.22a8 8 0 0 0 5 1.78a8 8 0 0 0 8-8V96a16 16 0 0 0-16-16M66.55 137.78L40 159.25V48h128v88H71.58a8 8 0 0 0-5.03 1.78M216 207.25l-26.55-21.47a8 8 0 0 0-5-1.78H88v-32h80a16 16 0 0 0 16-16V96h32Z"></path></svg>
                        </Link>
                    </button>
                    <button @click="favorite()" class="bg-gray-200 h-9 px-4 rounded-md">
                        <svg v-if="contact.is_favorite === 0 || contact.is_favorite === false" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.5" d="M9.153 5.408C10.42 3.136 11.053 2 12 2c.947 0 1.58 1.136 2.847 3.408l.328.588c.36.646.54.969.82 1.182c.28.213.63.292 1.33.45l.636.144c2.46.557 3.689.835 3.982 1.776c.292.94-.546 1.921-2.223 3.882l-.434.507c-.476.557-.715.836-.822 1.18c-.107.345-.071.717.001 1.46l.066.677c.253 2.617.38 3.925-.386 4.506c-.766.582-1.918.051-4.22-1.009l-.597-.274c-.654-.302-.981-.452-1.328-.452c-.347 0-.674.15-1.329.452l-.595.274c-2.303 1.06-3.455 1.59-4.22 1.01c-.767-.582-.64-1.89-.387-4.507l.066-.676c.072-.744.108-1.116 0-1.46c-.106-.345-.345-.624-.821-1.18l-.434-.508c-1.677-1.96-2.515-2.941-2.223-3.882c.293-.941 1.523-1.22 3.983-1.776l.636-.144c.699-.158 1.048-.237 1.329-.45c.28-.213.46-.536.82-1.182l.328-.588Z"/></svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="#031b4d" stroke="#031b4d" stroke-width="1.5" d="M9.153 5.408C10.42 3.136 11.053 2 12 2c.947 0 1.58 1.136 2.847 3.408l.328.588c.36.646.54.969.82 1.182c.28.213.63.292 1.33.45l.636.144c2.46.557 3.689.835 3.982 1.776c.292.94-.546 1.921-2.223 3.882l-.434.507c-.476.557-.715.836-.822 1.18c-.107.345-.071.717.001 1.46l.066.677c.253 2.617.38 3.925-.386 4.506c-.766.582-1.918.051-4.22-1.009l-.597-.274c-.654-.302-.981-.452-1.328-.452c-.347 0-.674.15-1.329.452l-.595.274c-2.303 1.06-3.455 1.59-4.22 1.01c-.767-.582-.64-1.89-.387-4.507l.066-.676c.072-.744.108-1.116 0-1.46c-.106-.345-.345-.624-.821-1.18l-.434-.508c-1.677-1.96-2.515-2.941-2.223-3.882c.293-.941 1.523-1.22 3.983-1.776l.636-.144c.699-.158 1.048-.237 1.329-.45c.28-.213.46-.536.82-1.182l.328-.588Z"/></svg>
                    </button>
                    <Dropdown class="h-100" :align="'right'">
                        <button class="h-9 bg-gray-200 py-2 px-3 rounded-md flex items-center">
                            <span class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 16 16"><path fill="currentColor" d="M4 8a2 2 0 1 1-4 0a2 2 0 0 1 4 0Zm6 0a2 2 0 1 1-4 0a2 2 0 0 1 4 0Zm4 2a2 2 0 1 0 0-4a2 2 0 0 0 0 4Z"/></svg>
                            </span>
                        </button>
                        <template #items>
                            <DropdownItemGroup>
                                <DropdownItem as="button" @click="deleteRow()">{{ $t('Delete') }}</DropdownItem>
                            </DropdownItemGroup>
                        </template>
                    </Dropdown>
                </div>
            </div>
        </div>
        <div class="pr-20 border-gray-300 border-b py-4">
            <div class="grid grid-cols-2 space-x-8 text-[14px]">
                <div class="text-right text-slate-500 pb-2">
                    <span>{{ $t('Full name') }}</span>
                </div>
                <div>
                    <span>{{ contact.full_name }}</span>
                </div>
                <div class="text-right text-slate-500 pb-2">
                    <span>{{ $t('Group') }}</span>
                </div>
                <div>
                    <span class="">
                        {{ contact.contact_groups?.length ? contact.contact_groups.map(group => group.name).join(', ') : $t('not set') }}
                    </span>
                </div>
                <div class="text-right text-slate-500 pb-2">
                    <span>{{ $t('Email') }}</span>
                </div>
                <div>
                    <span>{{ contact.email ?? $t('not set') }}</span>
                </div>
                <div class="text-right text-slate-500 pb-2">
                    <span>{{ $t('Contact') }}</span>
                </div>
                <div>
                    <span>{{ contact.formatted_phone_number }}</span>
                </div>
            </div>
        </div>
        <div v-if="locationSettings === 'before' && fields.length > 0" class="pr-20 border-gray-300 border-b py-4">
            <div v-for="(input, index) in props.fields" class="grid grid-cols-2 space-x-8 text-[14px]">
                <div class="text-right text-slate-500 pb-2">
                    <span>{{ $t(input.name) }}</span>
                </div>
                <div>
                    <span v-if="metadata && metadata[input.name] != null">{{ metadata[input.name] }}</span>
                    <span v-else class="p-1 bg-slate-100 text-xs rounded-md text-gray-600">{{ $t('not set') }}</span>
                </div>
            </div>
        </div>
        <div class="pr-20 py-4">
            <div class="grid grid-cols-2 space-x-8 text-[14px]">
                <div class="text-right text-slate-500 pb-2">
                    <span>{{ $t('Street') }}</span>
                </div>
                <div>
                    <span>{{ getAddressDetail(contact.address, 'street') }}</span>
                </div>
                <div class="text-right text-slate-500 pb-2">
                    <span>{{ $t('City') }}</span>
                </div>
                <div>
                    <span>{{ getAddressDetail(contact.address, 'city') }}</span>
                </div>
                <div class="text-right text-slate-500 pb-2">
                    <span>{{ $t('State') }}</span>
                </div>
                <div>
                    <span>{{ getAddressDetail(contact.address, 'state') }}</span>
                </div>
                <div class="text-right text-slate-500 pb-2">
                    <span>{{ $t('Zip code') }}</span>
                </div>
                <div>
                    <span>{{ getAddressDetail(contact.address, 'zip') }}</span>
                </div>
                <div class="text-right text-slate-500 pb-2">
                    <span>{{ $t('Country') }}</span>
                </div>
                <div>
                    <span>{{ getAddressDetail(contact.address, 'country') }}</span>
                </div>
            </div>
        </div>
        <div v-if="locationSettings === 'after' && fields.length > 0" class="pr-20 border-gray-300 border-t py-4">
            <div v-for="(input, index) in props.fields" class="grid grid-cols-2 space-x-8 text-[14px]">
                <div class="text-right text-slate-500 pb-2">
                    <span>{{ $t(input.name) }}</span>
                </div>
                <div>
                    <span v-if="metadata && metadata[input.name] != null">{{ metadata[input.name] }}</span>
                    <span v-else class="p-1 bg-slate-100 text-xs rounded-md text-gray-600">{{ $t('not set') }}</span>
                </div>
            </div>
        </div>
    </div>
</template> -->


<!-- ========================================== NEW UI CODE ==================================== -->

<script setup>
import { ref, watchEffect } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownItemGroup from '@/Components/DropdownItemGroup.vue';
import DropdownItem from '@/Components/DropdownItem.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps(['contact', 'fields', 'locationSettings']);
const contact = ref(props.contact);
const metadata = (props.contact.metadata !== null && props.contact.metadata !== '') ? JSON.parse(props.contact.metadata) : {};

watchEffect(() => {
    contact.value = props.contact;
});

const favorite = async () => {
    contact.value.is_favorite = !contact.value.is_favorite;
    router.put('/contacts/favorite/' + contact.value.uuid, { favorite: contact.value.is_favorite });
}

const deleteRow = async () => {
    router.visit('/contacts', {
        method: 'delete',
        data: { 'uuids': [contact.value.uuid] },
        preserveState: true
    })
}

const getAddressDetail = (value, key) => {
    const address = JSON.parse(value);
    return address?.[key] && address?.[key] != 'Not Set' ? address?.[key] : trans('not set');
}
</script>

<template>
    <div class="h-full overflow-y-auto bg-gradient-to-br from-gray-50 to-orange-50/30">
        <div class="max-w-4xl mx-auto p-8">

            <!-- Hero Section -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-6">
                <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        <div class="relative">
                            <img v-if="contact.avatar"
                                class="w-32 h-32 rounded-full object-cover ring-4 ring-[#ff5100]/20"
                                :src="contact.avatar">
                            <div v-else
                                class="w-32 h-32 rounded-full bg-gradient-to-br from-[#ff5100] to-orange-400 flex items-center justify-center">
                                <svg class="w-20 h-20 text-white" viewBox="0 0 24 24" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M18.685 19.097A9.723 9.723 0 0021.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 003.065 7.097A9.716 9.716 0 0012 21.75a9.716 9.716 0 006.685-2.653zm-12.54-1.285A7.486 7.486 0 0112 15a7.486 7.486 0 015.855 2.812A8.224 8.224 0 0112 20.25a8.224 8.224 0 01-5.855-2.438zM15.75 9a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <button @click="favorite()"
                                class="absolute bottom-0 right-0 w-10 h-10 bg-white rounded-full shadow-lg flex items-center justify-center hover:scale-110 transition-transform">
                                <svg v-if="contact.is_favorite === 0 || contact.is_favorite === false"
                                    xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                                    class="text-gray-400">
                                    <path fill="none" stroke="currentColor" stroke-width="2"
                                        d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2L9.19 8.63L2 9.24l5.46 4.73L5.82 21z" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                    viewBox="0 0 24 24">
                                    <path fill="#ff5100"
                                        d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2L9.19 8.63L2 9.24l5.46 4.73L5.82 21z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Info & Actions -->
                    <div class="flex-1 text-center md:text-left">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ contact.full_name }}</h1>
                        <p class="text-gray-500 mb-6 flex items-center justify-center md:justify-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24c1.12.37 2.33.57 3.57.57c.55 0 1 .45 1 1V20c0 .55-.45 1-1 1c-9.39 0-17-7.61-17-17c0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1c0 1.25.2 2.45.57 3.57c.11.35.03.74-.25 1.02l-2.2 2.2z" />
                            </svg>
                            <span>{{ contact.formatted_phone_number }}</span>
                        </p>

                        <div class="flex flex-wrap gap-3 justify-center md:justify-start">
                            <Link :href="'/contacts/' + contact.uuid + '?edit=true'"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#ff5100] hover:bg-[#e64900] text-white rounded-xl font-medium transition-all shadow-md hover:shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a.9959.9959 0 0 0-1.41 0l-1.83 1.83l3.75 3.75l1.83-1.83z" />
                            </svg>
                            {{ $t('Edit') }}
                            </Link>

                            <Link :href="'/chats/' + contact.uuid"
                                class="inline-flex items-center gap-2 px-6 py-2.5 border-2 border-gray-200 hover:border-[#ff5100] rounded-xl font-medium text-gray-700 hover:text-[#ff5100] transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 256 256">
                                <path fill="currentColor"
                                    d="M216 80h-32V48a16 16 0 0 0-16-16H40a16 16 0 0 0-16 16v128a8 8 0 0 0 13 6.22L72 154v30a16 16 0 0 0 16 16h93.59L219 230.22a8 8 0 0 0 5 1.78a8 8 0 0 0 8-8V96a16 16 0 0 0-16-16M66.55 137.78L40 159.25V48h128v88H71.58a8 8 0 0 0-5.03 1.78M216 207.25l-26.55-21.47a8 8 0 0 0-5-1.78H88v-32h80a16 16 0 0 0 16-16V96h32Z" />
                            </svg>
                            {{ $t('Message') }}
                            </Link>

                            <Dropdown :align="'right'">
                                <button
                                    class="p-2.5 border-2 border-gray-200 hover:border-[#ff5100] rounded-xl transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 16 16"
                                        class="text-gray-600">
                                        <path fill="currentColor"
                                            d="M8 2.5a1.22 1.22 0 0 1 1.25 1.17A1.21 1.21 0 0 1 8 4.84a1.21 1.21 0 0 1-1.25-1.17A1.22 1.22 0 0 1 8 2.5m0 8.66a1.17 1.17 0 1 1-1.25 1.17A1.21 1.21 0 0 1 8 11.16m0-4.33a1.17 1.17 0 1 1 0 2.34a1.17 1.17 0 1 1 0-2.34" />
                                    </svg>
                                </button>
                                <template #items>
                                    <DropdownItemGroup>
                                        <DropdownItem as="button" @click="deleteRow()">{{ $t('Delete') }}</DropdownItem>
                                    </DropdownItemGroup>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Details -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-[#ff5100]/10 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            class="text-[#ff5100]">
                            <path fill="currentColor"
                                d="M12 12q-1.65 0-2.825-1.175T8 8q0-1.65 1.175-2.825T12 4q1.65 0 2.825 1.175T16 8q0 1.65-1.175 2.825T12 12Zm-8 8v-2.8q0-.85.438-1.563T5.6 14.55q1.55-.775 3.15-1.163T12 13q1.65 0 3.25.388t3.15 1.162q.725.375 1.163 1.088T20 17.2V20H4Z" />
                        </svg>
                    </div>
                    {{ $t('Contact Information') }}
                </h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-500">{{ $t('Full name') }}</label>
                        <p class="text-gray-900">{{ contact.full_name }}</p>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-500">{{ $t('Group') }}</label>
                        <p class="text-gray-900">
                            <span v-if="contact.contact_groups?.length"
                                class="inline-flex items-center gap-1 px-3 py-1 bg-[#ff5100]/10 text-[#ff5100] rounded-lg text-sm font-medium">
                                {{contact.contact_groups.map(group => group.name).join(', ')}}
                            </span>
                            <span v-else class="text-gray-400">{{ $t('not set') }}</span>
                        </p>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-500">{{ $t('Email') }}</label>
                        <p class="text-gray-900">{{ contact.email ?? $t('not set') }}</p>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-500">{{ $t('Contact') }}</label>
                        <p class="text-gray-900">{{ contact.formatted_phone_number }}</p>
                    </div>
                </div>
            </div>

            <!-- Custom Fields (Before) -->
            <div v-if="locationSettings === 'before' && fields.length > 0"
                class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-6">
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
                <div class="grid md:grid-cols-2 gap-6">
                    <div v-for="(input, index) in props.fields" :key="index" class="space-y-1">
                        <label class="text-sm font-medium text-gray-500">{{ $t(input.name) }}</label>
                        <p v-if="metadata && metadata[input.name] != null" class="text-gray-900">{{ metadata[input.name]
                            }}</p>
                        <p v-else class="text-gray-400">{{ $t('not set') }}</p>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-12">
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
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-1 md:col-span-2">
                        <label class="text-sm font-medium text-gray-500">{{ $t('Street') }}</label>
                        <p class="text-gray-900">{{ getAddressDetail(contact.address, 'street') }}</p>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-500">{{ $t('City') }}</label>
                        <p class="text-gray-900">{{ getAddressDetail(contact.address, 'city') }}</p>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-500">{{ $t('State') }}</label>
                        <p class="text-gray-900">{{ getAddressDetail(contact.address, 'state') }}</p>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-500">{{ $t('Zip code') }}</label>
                        <p class="text-gray-900">{{ getAddressDetail(contact.address, 'zip') }}</p>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-500">{{ $t('Country') }}</label>
                        <p class="text-gray-900">{{ getAddressDetail(contact.address, 'country') }}</p>
                    </div>
                </div>
            </div>

            <!-- Custom Fields (After) -->
            <div v-if="locationSettings === 'after' && fields.length > 0"
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
                <div class="grid md:grid-cols-2 gap-6">
                    <div v-for="(input, index) in props.fields" :key="index" class="space-y-1">
                        <label class="text-sm font-medium text-gray-500">{{ $t(input.name) }}</label>
                        <p v-if="metadata && metadata[input.name] != null" class="text-gray-900">{{ metadata[input.name]
                            }}</p>
                        <p v-else class="text-gray-400">{{ $t('not set') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>