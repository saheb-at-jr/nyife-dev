<!-- <script setup>
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import { Link, router } from "@inertiajs/vue3";
import Pagination from '@/Components/Pagination.vue';
import TicketStatusToggle from '@/Components/TicketStatusToggle.vue';
import SortDirectionToggle from '@/Components/SortDirectionToggle.vue';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object
    },
    rowCount: {
        type: Number,
        required: true,
    },
    ticketingIsEnabled: {
        type: Boolean
    },
    status: {
        type: String,
    },
    chatSortDirection: {
        type: String
    }
});

const isSearching = ref(false);
const emit = defineEmits(['view']);

const contentType = (metadata) => {
    const chatData = JSON.parse(metadata);
    return chatData.type;
}

const content = (metadata) => {
    return JSON.parse(metadata);
}

const getExtension = (fileFormat) => {
    const formatMap = {
        'text/plain': 'TXT',
        'application/pdf': 'PDF',
        'application/vnd.ms-powerpoint': 'PPT',
        'application/msword': 'DOC',
        'application/vnd.ms-excel': 'XLS',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'DOCX',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation': 'PPTX',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'XLSX',
    };

    return formatMap[fileFormat] || 'Unknown';
};

const getContactDisplayName = (metadata) => {
    const contacts = JSON.parse(metadata).contacts;
    if (contacts.length === 1) {
        const contact = contacts[0];
        return contact.name.formatted_name || `${contact.name.first_name} ${contact.name.last_name}`;
    } else if (contacts.length > 1) {
        const firstName = contacts[0].name.first_name;
        const otherContactsCount = contacts.length - 1;
        return `${firstName} and ${otherContactsCount} other contacts`;
    } else {
        return 'No contacts available';
    }
}

const formatTime = (time) => {
    if (!time) {
        return "Invalid time";
    } else {
        const currentTime = new Date();
        const targetTime = new Date(time);

        // Check if the time is today
        if (
            targetTime.getDate() === currentTime.getDate() &&
            targetTime.getMonth() === currentTime.getMonth() &&
            targetTime.getFullYear() === currentTime.getFullYear()
        ) {
            return formatDate(targetTime, 'h:mm a');
        }

        // Check if the time is yesterday
        const yesterday = new Date();
        yesterday.setDate(currentTime.getDate() - 1);
        if (
            targetTime.getDate() === yesterday.getDate() &&
            targetTime.getMonth() === yesterday.getMonth() &&
            targetTime.getFullYear() === yesterday.getFullYear()
        ) {
            return 'Yesterday';
        }

        // If beyond yesterday, return the time in the format d/m/y
        return formatDate(targetTime, 'd/m/y');
    }
};

const formatDate = (date, format) => {
    let options = null;
    if (format === 'h:mm a') {
        options = { hour12: true, hour: 'numeric', minute: 'numeric' };
    } else {
        options = { day: 'numeric', month: 'numeric', year: 'numeric' };
    }

    return new Intl.DateTimeFormat('en-US', options).format(date);
};

const queryParamsString = window.location.search;

// Watch for changes in the query parameters
watch(() => queryParamsString,
    (newQuery, oldQuery) => {
        console.log('Query parameters changed:', newQuery);
        // Perform actions based on query parameter changes
    }
);

const params = ref({
    search: props.filters.search,
});

const search = debounce(() => {
    isSearching.value = true;
    runSearch();
}, 1000);

const runSearch = () => {
    const url = window.location.pathname;

    router.visit(url, {
        method: 'get',
        data: params.value,
    })
}

const clearSearch = () => {
    params.value.search = null;
    runSearch();
}
</script>


<script>
export default {
    data() {
        return {
            days: 2,
            customDays: null,
            loading: false,
        };
    },
    methods: {
        exportExcel() {
            let exportDays = this.days;
            if (this.days === 'custom') {
                if (!this.customDays || this.customDays < 1 || this.customDays > 30) {
                    alert('Please enter a number between 1 and 30');
                    return;
                }
                exportDays = this.customDays;
            }

            this.loading = true;

            // Build URL
            const url = `/chats?export=excel&days=${exportDays}`;
            window.location.href = url; // trigger download

            // Reset loading after a short delay (optional)
            setTimeout(() => { this.loading = false; }, 3000);
        }
    }
}
</script>


<template>
    <div class="px-4 py-4 border-b">
        <div class="flex items-center justify-between space-x-1 text-xl">
            <div class="flex space-x-1">
                <h2>{{ $t('Chats') }}</h2>
                <span class="text-slate-500">{{ rowCount }}</span>
            </div>
        </div>
        <div class="bg-slate-50 rounded-md mt-3 flex items-center py-[2px]">
            <div class="pl-3 py-2">
                <svg class="text-slate-600" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                    viewBox="0 0 24 24">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M3 10a7 7 0 1 0 14 0a7 7 0 1 0-14 0m18 11l-6-6" />
                </svg>
            </div>
            <input @input="search" v-model="params.search"
                class="w-full bg-slate-50 outline-none rounded-xl py-2 pl-2 mr-2 text-sm" type="text"
                :placeholder="$t('Search name or number...')">
            <button v-if="isSearching === false && params.search" @click="clearSearch" type="button" class="pr-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path fill="currentColor"
                        d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2zm3.7 12.3c.4.4.4 1 0 1.4c-.4.4-1 .4-1.4 0L12 13.4l-2.3 2.3c-.4.4-1 .4-1.4 0c-.4-.4-.4-1 0-1.4l2.3-2.3l-2.3-2.3c-.4-.4-.4-1 0-1.4c.4-.4 1-.4 1.4 0l2.3 2.3l2.3-2.3c.4-.4 1-.4 1.4 0c.4.4.4 1 0 1.4L13.4 12l2.3 2.3z" />
                </svg>
            </button>
            <span v-if="isSearching" class="pr-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0">
                        <animateTransform attributeName="transform" calcMode="discrete" dur="2.4s"
                            repeatCount="indefinite" type="rotate" values="0 12 12;90 12 12;180 12 12;270 12 12" />
                        <animate attributeName="opacity" dur="0.6s" keyTimes="0;0.5;1" repeatCount="indefinite"
                            values="1;1;0" />
                    </circle>
                    <circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0">
                        <animateTransform attributeName="transform" begin="0.2s" calcMode="discrete" dur="2.4s"
                            repeatCount="indefinite" type="rotate" values="30 12 12;120 12 12;210 12 12;300 12 12" />
                        <animate attributeName="opacity" begin="0.2s" dur="0.6s" keyTimes="0;0.5;1"
                            repeatCount="indefinite" values="1;1;0" />
                    </circle>
                    <circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0">
                        <animateTransform attributeName="transform" begin="0.4s" calcMode="discrete" dur="2.4s"
                            repeatCount="indefinite" type="rotate" values="60 12 12;150 12 12;240 12 12;330 12 12" />
                        <animate attributeName="opacity" begin="0.4s" dur="0.6s" keyTimes="0;0.5;1"
                            repeatCount="indefinite" values="1;1;0" />
                    </circle>
                </svg>
            </span>
        </div>

        <p class="mb-2 text-gray-600 text-sm">
            Here you can download <span class="font-medium">responded inbound chats</span> as an Excel file.
            Choose a time range (last 1 day, 2 days, or set a custom number of days up to 30), then click
            <b>Download</b>.
        </p>

        <form @submit.prevent="exportExcel" ref="exportForm" class="flex items-center">
            <select v-model="days" ref="daysSelect"
                class="px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="1">Last 1 day</option>
                <option value="2">Last 2 days</option>
                <option value="custom">Custom</option>
            </select>

            <input type="number" v-if="days === 'custom'" v-model.number="customDays" min="1" max="30"
                class="ml-2 w-24 px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Days" />

            <button
                class="ml-4 px-2 py-1 text-sm bg-blue-600 text-white rounded shadow hover:bg-blue-700 disabled:opacity-50"
                type="submit" :disabled="loading">
                {{ loading ? 'Downloading...' : 'Download' }}
            </button>
        </form>

        <div v-if="ticketingIsEnabled" class="grid grid-cols-2 mt-4 items-center w-full">
            <TicketStatusToggle :status="status" :rowCount="rowCount" />
            <div class="flex ml-auto gap-x-1">
                <SortDirectionToggle :direction="props.chatSortDirection" :url="'/chats/update-sort-direction'" />
            </div>
        </div>
    </div>
    <div class="flex-grow overflow-y-auto h-[65vh]" ref="scrollContainer">
        <Link :href="'/chats/' + contact.uuid + '?page=' + props.rows.meta.current_page"
            class="block border-b group-hover:pr-0" :class="contact.unread_messages > 0 ? 'bg-green-50' : ''"
            v-for="(contact, index) in rows.data" :key="index">
        <div class="flex space-x-2 hover:bg-gray-50 cursor-pointer py-3 px-4">
            <div class="w-[15%]">
                <img v-if="contact.avatar" class="rounded-full w-10 h-10" :src="contact.avatar">
                <div v-else class="rounded-full w-10 h-10 flex items-center justify-center bg-slate-200 capitalize">{{
                    contact.full_name.substring(0, 1) }}</div>
            </div>
            <div class="w-[85%]">
                <div class="flex justify-between">
                    <h3 class="truncate">{{ contact.full_name }}</h3>
                    <span class="self-center text-slate-500 text-xs">{{ formatTime(contact?.last_chat?.created_at)
                    }}</span>
                </div>
                <div v-if="contact?.last_chat?.deleted_at === null" class="flex justify-between">
                    <div v-if="contentType(contact?.last_chat?.metadata) === 'text'"
                        class="text-slate-500 text-xs truncate self-end"> {{
                            content(contact?.last_chat?.metadata).text.body }}</div>
                    <div v-if="contentType(contact?.last_chat?.metadata) === 'button'"
                        class="text-slate-500 text-xs truncate self-end"> {{
                            content(contact?.last_chat?.metadata).button.text }}</div>
                    <div v-if="contentType(contact?.last_chat?.metadata) === 'interactive'"
                        class="text-slate-500 text-xs truncate self-end"> {{
                            content(contact?.last_chat?.metadata).interactive?.button_reply?.title ||
                            content(contact?.last_chat?.metadata).interactive?.list_reply?.title }}</div>
                    <div v-if="contentType(contact?.last_chat?.metadata) === 'image'"
                        class="text-slate-500 text-xs truncate self-end">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.9 13.98l2.1 2.53l3.1-3.99c.2-.26.6-.26.8.01l3.51 4.68a.5.5 0 0 1-.4.8H6.02c-.42 0-.65-.48-.39-.81L8.12 14c.19-.26.57-.27.78-.02z" />
                            </svg>
                            <span class="ml-2">{{ $t('Photo') }}</span>
                        </div>
                    </div>
                    <div v-if="contentType(contact?.last_chat?.metadata) === 'document'"
                        class="text-slate-500 text-xs truncate self-end">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                <path fill="currentColor" fill-rule="evenodd"
                                    d="M14.25 2.5a.25.25 0 0 0-.25-.25H7A2.75 2.75 0 0 0 4.25 5v14A2.75 2.75 0 0 0 7 21.75h10A2.75 2.75 0 0 0 19.75 19V9.147a.25.25 0 0 0-.25-.25H15a.75.75 0 0 1-.75-.75V2.5Zm.75 9.75a.75.75 0 0 1 0 1.5H9a.75.75 0 0 1 0-1.5h6Zm0 4a.75.75 0 0 1 0 1.5H9a.75.75 0 0 1 0-1.5h6Z"
                                    clip-rule="evenodd" />
                                <path fill="currentColor"
                                    d="M15.75 2.824c0-.184.193-.301.336-.186c.121.098.23.212.323.342l3.013 4.197c.068.096-.006.22-.124.22H16a.25.25 0 0 1-.25-.25V2.824Z" />
                            </svg>
                            <span class="ml-2">{{ getExtension(contact?.last_chat?.media.type) }} {{ $t('File')
                            }}</span>
                        </div>
                    </div>
                    <div v-if="contentType(contact?.last_chat?.metadata) === 'video'"
                        class="text-slate-500 text-xs truncate self-end">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                <path fill="currentColor"
                                    d="M3.5 2.5A2.5 2.5 0 0 0 1 5v6a2.5 2.5 0 0 0 2.5 2.5h5A2.5 2.5 0 0 0 11 11V5a2.5 2.5 0 0 0-2.5-2.5h-5Zm10.684 1.61L12 5.893v4.215l2.184 1.78a.5.5 0 0 0 .816-.389v-7a.5.5 0 0 0-.816-.387Z" />
                            </svg>
                            <span class="ml-2">{{ $t('Video') }}</span>
                        </div>
                    </div>
                    <div v-if="contentType(contact?.last_chat?.metadata) === 'audio'"
                        class="text-slate-500 text-xs truncate self-end">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 512 512">
                                <path fill="currentColor"
                                    d="M256 80C149.9 80 62.4 159.4 49.6 262c9.4-3.8 19.6-6 30.4-6c26.5 0 48 21.5 48 48v128c0 26.5-21.5 48-48 48c-44.2 0-80-35.8-80-80V288C0 146.6 114.6 32 256 32s256 114.6 256 256v112c0 44.2-35.8 80-80 80c-26.5 0-48-21.5-48-48V304c0-26.5 21.5-48 48-48c10.8 0 21 2.1 30.4 6C449.6 159.4 362.1 80 256 80z" />
                            </svg>
                            <span class="ml-2">{{ $t('Audio') }}</span>
                        </div>
                    </div>
                    <div v-if="contentType(contact?.last_chat?.metadata) === 'sticker'"
                        class="text-slate-500 text-xs truncate self-end">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 256 256">
                                <path fill="currentColor"
                                    d="M168 32H88a56.06 56.06 0 0 0-56 56v80a56.06 56.06 0 0 0 56 56h48a8.07 8.07 0 0 0 2.53-.41c26.23-8.75 76.31-58.83 85.06-85.06A8.07 8.07 0 0 0 224 136V88a56.06 56.06 0 0 0-56-56Zm-32 175.42V176a40 40 0 0 1 40-40h31.42c-9.26 21.55-49.87 62.16-71.42 71.42Z" />
                            </svg>
                            <span class="ml-2">{{ $t('Sticker') }}</span>
                        </div>
                    </div>
                    <div v-if="contentType(contact?.last_chat?.metadata) === 'contacts'"
                        class="text-slate-500 text-xs truncate self-end">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                <path fill="currentColor"
                                    d="M3 14s-1 0-1-1s1-4 6-4s6 3 6 4s-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6a3 3 0 0 0 0 6Z" />
                            </svg>
                            <span class="ml-2">
                                {{ getContactDisplayName(contact?.last_chat?.metadata) }}
                            </span>
                        </div>
                    </div>
                    <div v-if="contentType(contact?.last_chat?.metadata) === 'location'"
                        class="text-slate-500 text-xs truncate self-end">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                <path fill="currentColor"
                                    d="M9.156 14.544C10.899 13.01 14 9.876 14 7A6 6 0 0 0 2 7c0 2.876 3.1 6.01 4.844 7.544a1.736 1.736 0 0 0 2.312 0ZM6 7a2 2 0 1 1 4 0a2 2 0 0 1-4 0Z" />
                            </svg>
                            <span class="ml-2">{{ $t('Location') }}</span>
                        </div>
                    </div>
                    <span v-if="contact.unread_messages > 0"
                        class="bg-green-600 text-white rounded-md py-[1px] px-[8px] min-w-10 text-[10px] flex items-center justify-center">{{
                            contact.unread_messages }}</span>
                </div>
            </div>
        </div>
        </Link>
    </div>
    <div class="px-4 pb-4">
        <Pagination class="mt-3" :pagination="rows.meta" />
    </div>
</template> -->

<!-- ========================================== NEW UI CODE ==================================== -->

<script setup>
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import { Link, router } from "@inertiajs/vue3";
import Pagination from '@/Components/Pagination.vue';
import TicketStatusToggle from '@/Components/TicketStatusToggle.vue';
import SortDirectionToggle from '@/Components/SortDirectionToggle.vue';

const props = defineProps({
    rows: { type: Object, required: true },
    filters: { type: Object },
    rowCount: { type: Number, required: true },
    ticketingIsEnabled: { type: Boolean },
    status: { type: String },
    chatSortDirection: { type: String }
});

const isSearching = ref(false);
const emit = defineEmits(['view']);

const contentType = (metadata) => JSON.parse(metadata).type;
const content = (metadata) => JSON.parse(metadata);

const getExtension = (fileFormat) => {
    const formatMap = {
        'text/plain': 'TXT', 'application/pdf': 'PDF',
        'application/vnd.ms-powerpoint': 'PPT', 'application/msword': 'DOC',
        'application/vnd.ms-excel': 'XLS',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'DOCX',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation': 'PPTX',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'XLSX',
    };
    return formatMap[fileFormat] || 'Unknown';
};

const getContactDisplayName = (metadata) => {
    const contacts = JSON.parse(metadata).contacts;
    if (contacts.length === 1) {
        const contact = contacts[0];
        return contact.name.formatted_name || `${contact.name.first_name} ${contact.name.last_name}`;
    } else if (contacts.length > 1) {
        return `${contacts[0].name.first_name} and ${contacts.length - 1} other contacts`;
    }
    return 'No contacts available';
}

const formatTime = (time) => {
    if (!time) return "Invalid time";

    const currentTime = new Date();
    const targetTime = new Date(time);

    if (targetTime.getDate() === currentTime.getDate() &&
        targetTime.getMonth() === currentTime.getMonth() &&
        targetTime.getFullYear() === currentTime.getFullYear()) {
        return formatDate(targetTime, 'h:mm a');
    }

    const yesterday = new Date();
    yesterday.setDate(currentTime.getDate() - 1);
    if (targetTime.getDate() === yesterday.getDate() &&
        targetTime.getMonth() === yesterday.getMonth() &&
        targetTime.getFullYear() === yesterday.getFullYear()) {
        return 'Yesterday';
    }

    return formatDate(targetTime, 'd/m/y');
};

const formatDate = (date, format) => {
    const options = format === 'h:mm a'
        ? { hour12: true, hour: 'numeric', minute: 'numeric' }
        : { day: 'numeric', month: 'numeric', year: 'numeric' };
    return new Intl.DateTimeFormat('en-US', options).format(date);
};

const params = ref({
    search: props.filters.search,
});

const search = debounce(() => {
    isSearching.value = true;
    runSearch();
}, 1000);

const runSearch = () => {
    const url = window.location.pathname;
    router.visit(url, {
        method: 'get',
        data: params.value,
    })
}

const clearSearch = () => {
    params.value.search = null;
    runSearch();
}
</script>

<script>
export default {
    data() {
        return {
            days: 2,
            customDays: null,
            loading: false,
        };
    },
    methods: {
        exportExcel() {
            let exportDays = this.days;
            if (this.days === 'custom') {
                if (!this.customDays || this.customDays < 1 || this.customDays > 30) {
                    alert('Please enter a number between 1 and 30');
                    return;
                }
                exportDays = this.customDays;
            }
            this.loading = true;
            const url = `/chats?export=excel&days=${exportDays}`;
            window.location.href = url;
            setTimeout(() => { this.loading = false; }, 3000);
        }
    }
}
</script>

<template>
    <!-- Header -->
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $t('Chats') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ rowCount }} {{ $t('conversations') }}</p>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="relative mb-4">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                    class="text-gray-400">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="m15 15l6 6m-11-4a7 7 0 1 1 0-14a7 7 0 0 1 0 14Z" />
                </svg>
            </div>
            <input @input="search" v-model="params.search" type="text"
                class="w-full pl-12 pr-12 py-3 bg-gray-50 border-0 rounded-xl text-sm focus:ring-2 focus:ring-[#ff5100] focus:bg-white transition-all"
                :placeholder="$t('Search name or number...')">
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <button v-if="isSearching === false && params.search" @click="clearSearch" type="button"
                    class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2zm3.7 12.3c.4.4.4 1 0 1.4c-.4.4-1 .4-1.4 0L12 13.4l-2.3 2.3c-.4.4-1 .4-1.4 0c-.4-.4-.4-1 0-1.4l2.3-2.3l-2.3-2.3c-.4-.4-.4-1 0-1.4c.4-.4 1-.4 1.4 0l2.3 2.3l2.3-2.3c.4-.4 1-.4 1.4 0c.4.4.4 1 0 1.4L13.4 12l2.3 2.3z" />
                    </svg>
                </button>
                <span v-if="isSearching" class="text-[#ff5100]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                        <circle cx="12" cy="3.5" r="1.5" fill="currentColor" opacity="0">
                            <animateTransform attributeName="transform" calcMode="discrete" dur="2.4s"
                                repeatCount="indefinite" type="rotate" values="0 12 12;90 12 12;180 12 12;270 12 12" />
                            <animate attributeName="opacity" dur="0.6s" keyTimes="0;0.5;1" repeatCount="indefinite"
                                values="1;1;0" />
                        </circle>
                    </svg>
                </span>
            </div>
        </div>

        <!-- Export Section -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
            <p class="text-sm text-blue-900 mb-3">
                {{ $t('Download') }} <span class="font-semibold">{{ $t('responded inbound chats') }}</span> {{ $t(`as
                Excel`) }}
            </p>
            <form @submit.prevent="exportExcel" class="flex items-center gap-3">
                <select v-model="days"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#ff5100] focus:border-[#ff5100]">
                    <option value="1">{{ $t('Last 1 day') }}</option>
                    <option value="2">{{ $t('Last 2 days') }}</option>
                    <option value="custom">{{ $t('Custom') }}</option>
                </select>

                <input v-if="days === 'custom'" v-model.number="customDays" type="number" min="1" max="30"
                    class="w-24 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#ff5100] focus:border-[#ff5100]"
                    :placeholder="$t('Days')" />

                <button type="submit" :disabled="loading"
                    class="px-4 py-2 bg-[#ff5100] hover:bg-[#e64900] text-white rounded-lg text-sm font-medium transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ loading ? $t('Downloading...') : $t('Download') }}
                </button>
            </form>
        </div>

        <!-- Filters -->
        <div v-if="ticketingIsEnabled" class="flex items-center justify-between">
            <TicketStatusToggle :status="status" :rowCount="rowCount" />
            <SortDirectionToggle :direction="props.chatSortDirection" :url="'/chats/update-sort-direction'" />
        </div>
    </div>

    <!-- Chat List -->
    <div class="flex-grow overflow-y-auto h-[calc(100vh-380px)]">
        <Link :href="'/chats/' + contact.uuid + '?page=' + props.rows.meta.current_page"
            v-for="(contact, index) in rows.data" :key="index"
            class="block border-b border-gray-100 hover:bg-gray-50 transition-colors"
            :class="contact.unread_messages > 0 ? 'bg-green-50/50' : ''">
        <div class="flex items-center gap-3 p-4">
            <!-- Avatar -->
            <div class="flex-shrink-0">
                <img v-if="contact.avatar" class="w-12 h-12 rounded-full object-cover ring-2 ring-gray-100"
                    :src="contact.avatar">
                <div v-else
                    class="w-12 h-12 rounded-full bg-gradient-to-br from-[#ff5100] to-orange-400 flex items-center justify-center text-white font-semibold text-lg capitalize">
                    {{ contact.full_name.substring(0, 1) }}
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="font-semibold text-gray-900 truncate">{{ contact.full_name }}</h3>
                    <span class="text-xs text-gray-500 ml-2 flex-shrink-0">{{ formatTime(contact?.last_chat?.created_at)
                        }}</span>
                </div>

                <div v-if="contact?.last_chat?.deleted_at === null" class="flex items-center justify-between">
                    <!-- Message Preview -->
                    <div class="text-sm text-gray-600 truncate flex-1 flex items-center gap-2">
                        <!-- Text -->
                        <template v-if="contentType(contact?.last_chat?.metadata) === 'text'">
                            <span class="truncate">{{ content(contact?.last_chat?.metadata).text.body }}</span>
                        </template>

                        <!-- Button -->
                        <template v-else-if="contentType(contact?.last_chat?.metadata) === 'button'">
                            <span class="truncate">{{ content(contact?.last_chat?.metadata).button.text }}</span>
                        </template>

                        <!-- Interactive -->
                        <template v-else-if="contentType(contact?.last_chat?.metadata) === 'interactive'">
                            <span class="truncate">{{
                                content(contact?.last_chat?.metadata).interactive?.button_reply?.title ||
                                content(contact?.last_chat?.metadata).interactive?.list_reply?.title }}</span>
                        </template>

                        <!-- Image -->
                        <template v-else-if="contentType(contact?.last_chat?.metadata) === 'image'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                class="flex-shrink-0">
                                <path fill="currentColor"
                                    d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.9 13.98l2.1 2.53l3.1-3.99c.2-.26.6-.26.8.01l3.51 4.68a.5.5 0 0 1-.4.8H6.02c-.42 0-.65-.48-.39-.81L8.12 14c.19-.26.57-.27.78-.02z" />
                            </svg>
                            <span>{{ $t('Photo') }}</span>
                        </template>

                        <!-- Document -->
                        <template v-else-if="contentType(contact?.last_chat?.metadata) === 'document'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                class="flex-shrink-0">
                                <path fill="currentColor" fill-rule="evenodd"
                                    d="M14.25 2.5a.25.25 0 0 0-.25-.25H7A2.75 2.75 0 0 0 4.25 5v14A2.75 2.75 0 0 0 7 21.75h10A2.75 2.75 0 0 0 19.75 19V9.147a.25.25 0 0 0-.25-.25H15a.75.75 0 0 1-.75-.75V2.5Z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>{{ getExtension(contact?.last_chat?.media.type) }} {{ $t('File') }}</span>
                        </template>

                        <!-- Video -->
                        <template v-else-if="contentType(contact?.last_chat?.metadata) === 'video'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                class="flex-shrink-0">
                                <path fill="currentColor"
                                    d="M3.5 2.5A2.5 2.5 0 0 0 1 5v6a2.5 2.5 0 0 0 2.5 2.5h5A2.5 2.5 0 0 0 11 11V5a2.5 2.5 0 0 0-2.5-2.5h-5Z" />
                            </svg>
                            <span>{{ $t('Video') }}</span>
                        </template>

                        <!-- Audio -->
                        <template v-else-if="contentType(contact?.last_chat?.metadata) === 'audio'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 512 512"
                                class="flex-shrink-0">
                                <path fill="currentColor"
                                    d="M256 80C149.9 80 62.4 159.4 49.6 262c9.4-3.8 19.6-6 30.4-6c26.5 0 48 21.5 48 48v128c0 26.5-21.5 48-48 48c-44.2 0-80-35.8-80-80V288C0 146.6 114.6 32 256 32s256 114.6 256 256v112c0 44.2-35.8 80-80 80c-26.5 0-48-21.5-48-48V304c0-26.5 21.5-48 48-48c10.8 0 21 2.1 30.4 6C449.6 159.4 362.1 80 256 80z" />
                            </svg>
                            <span>{{ $t('Audio') }}</span>
                        </template>

                        <!-- Sticker -->
                        <template v-else-if="contentType(contact?.last_chat?.metadata) === 'sticker'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 256 256"
                                class="flex-shrink-0">
                                <path fill="currentColor"
                                    d="M168 32H88a56.06 56.06 0 0 0-56 56v80a56.06 56.06 0 0 0 56 56h48a8.07 8.07 0 0 0 2.53-.41c26.23-8.75 76.31-58.83 85.06-85.06A8.07 8.07 0 0 0 224 136V88a56.06 56.06 0 0 0-56-56Z" />
                            </svg>
                            <span>{{ $t('Sticker') }}</span>
                        </template>

                        <!-- Contacts -->
                        <template v-else-if="contentType(contact?.last_chat?.metadata) === 'contacts'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                class="flex-shrink-0">
                                <path fill="currentColor" d="M3 14s-1 0-1-1s1-4 6-4s6 3 6 4s-1 1-1 1H3Z" />
                            </svg>
                            <span>{{ getContactDisplayName(contact?.last_chat?.metadata) }}</span>
                        </template>

                        <!-- Location -->
                        <template v-else-if="contentType(contact?.last_chat?.metadata) === 'location'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                class="flex-shrink-0">
                                <path fill="currentColor"
                                    d="M9.156 14.544C10.899 13.01 14 9.876 14 7A6 6 0 0 0 2 7c0 2.876 3.1 6.01 4.844 7.544a1.736 1.736 0 0 0 2.312 0Z" />
                            </svg>
                            <span>{{ $t('Location') }}</span>
                        </template>
                    </div>

                    <!-- Unread Badge -->
                    <span v-if="contact.unread_messages > 0"
                        class="ml-2 bg-[#ff5100] text-white rounded-full px-2 py-0.5 text-xs font-semibold flex-shrink-0">
                        {{ contact.unread_messages }}
                    </span>
                </div>
            </div>
        </div>
        </Link>
    </div>

    <!-- Pagination -->
    <div class="p-4 border-t border-gray-100 bg-white">
        <Pagination :pagination="rows.meta" />
    </div>
</template>