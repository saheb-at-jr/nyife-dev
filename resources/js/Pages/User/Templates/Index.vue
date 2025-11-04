<template>
    <AppLayout>
        <div
            class="bg-white md:bg-inherit pt-0 px-4 md:pt-8 md:p-8 rounded-[5px] text-[#000] overflow-y-scroll capitalize">
            <div class="flex justify-between mt-8 md:mt-0">
                <!-- <div>
                    <h2 class="text-xl mb-1">{{ $t('Message templates') }}</h2>
                    <p class="mb-6 flex items-center text-sm leading-6 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z" />
                        </svg>
                        <span class="ml-1 mt-1">{{ $t('Add template') }}</span>
                    </p>
                </div> -->
                <!-- Modern Header Section -->
                <div class="mb-8 w-full">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#ff5100] to-[#ff7a3d] flex items-center justify-center shadow-lg shadow-[#ff5100]/25">
                                    <FileText class="w-6 h-6 text-white" />
                                </div>
                                {{ $t('Templates') }}
                            </h1>
                            <p class="text-gray-600">{{ $t('Manage your message templates') }}</p>
                        </div>

                        <div class="flex justify-end mb-6">
                            <div
                                class="inline-flex bg-white border border-gray-200 rounded-2xl shadow-md overflow-hidden">
                                <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
                                    class="flex items-center gap-2 px-6 py-3 font-semibold text-sm transition-all duration-300"
                                    :class="activeTab === tab.id
                                        ? 'bg-[#ff5100] text-white shadow-inner'
                                        : 'text-gray-600 hover:bg-gray-50'">
                                    <component :is="tab.icon" class="w-5 h-5"
                                        :class="activeTab === tab.id ? 'text-white' : 'text-[#ff5100] opacity-80'" />
                                    {{ tab.label }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div v-if="activeTab === 'whatsapp'">
                <div class="mb-3 w-full flex justify-end gap-x-2">
                    <button @click="syncTemplates"
                        class="bg-primary/90 hover:bg-primary rounded-xl px-4 py-3  text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 md:block hidden"
                        :class="!isSyncActive ? 'bg-indigo-600 hover:bg-indigo-500 shadow-sm' : 'bg-gray-200'"
                        :disabled="isSyncActive">
                        <span v-if="!isSyncActive">{{ $t('Sync templates') }}</span>
                        <svg v-else class="text-slate-600 animate-spin" xmlns="http://www.w3.org/2000/svg" width="20"
                            height="20" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M18.43 4.25a.76.76 0 0 0-.75.75v2.43l-.84-.84a7.24 7.24 0 0 0-12 2.78a.74.74 0 0 0 .46 1a.73.73 0 0 0 .25 0a.76.76 0 0 0 .71-.51a5.63 5.63 0 0 1 1.37-2.2a5.76 5.76 0 0 1 8.13 0l.84.84h-2.41a.75.75 0 0 0 0 1.5h4.24a.74.74 0 0 0 .75-.75V5a.75.75 0 0 0-.75-.75Zm.25 9.43a.76.76 0 0 0-1 .47a5.63 5.63 0 0 1-1.37 2.2a5.76 5.76 0 0 1-8.13 0l-.84-.84h2.47a.75.75 0 0 0 0-1.5H5.57a.74.74 0 0 0-.75.75V19a.75.75 0 0 0 1.5 0v-2.43l.84.84a7.24 7.24 0 0 0 12-2.78a.74.74 0 0 0-.48-.95Z" />
                        </svg>
                    </button>
                    <Link href="/templates/create"
                        class="bg-primary/90 hover:bg-primary rounded-xl px-4 py-3  text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 md:block hidden">
                    {{ $t('Create template') }}</Link>
                </div>
                <TemplateTable :rows="props.rows" />

            </div>
            <div v-else-if="activeTab === 'rcs'"
                class="p-6 bg-white border border-gray-100 rounded-2xl shadow-md text-gray-400 italic">
                RCS content coming soon...
            </div>

            <div v-else-if="activeTab === 'sms'"
                class="p-6 bg-white border border-gray-100 rounded-2xl shadow-md text-gray-400 italic">
                SMS content coming soon...
            </div>
        </div>
    </AppLayout>
</template>
<script setup>
import AppLayout from "./../Layout/App.vue";
import { router } from '@inertiajs/vue3';
import { Link } from "@inertiajs/vue3";
import TemplateTable from '@/Components/Tables/TemplateTable.vue';
import axios from 'axios';
import { ref } from 'vue';
import { FileText, MessageCircle, MessageSquare, MessageSquareText } from "lucide-vue-next";

const activeTab = ref('whatsapp')

const tabs = [
    { id: 'whatsapp', label: 'WhatsApp', icon: MessageCircle },
    { id: 'rcs', label: 'RCS', icon: MessageSquare },
    { id: 'sms', label: 'SMS', icon: MessageSquareText },
]

const props = defineProps({ rows: Object });
const isSyncActive = ref(false);
const syncTemplates = () => {
    isSyncActive.value = true;
    axios.get('/templates/sync')
        .then(function (response) {
            router.reload();
            setTimeout(() => {
                isSyncActive.value = false;
            }, 1500);
        })
        .catch(function (error) {
            isSyncActive.value = false;
        });
}
</script>