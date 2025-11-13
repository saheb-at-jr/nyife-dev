<!-- <template>
    <AppLayout v-slot:default="slotProps">
        <div class="max-w-[calc(100vw-265px)] md:h-[calc(100vh-64px)] md:flex md:flex-grow md:overflow-hidden">
            <div class="md:w-[30%] md:flex flex-col bg-white border-r border-l" :class="contact ? 'hidden' : ''">
                <ChatTable :rows="rows" :filters="props.filters" :rowCount="props.rowCount"
                    :ticketingIsEnabled="ticketingIsEnabled" :status="props?.status"
                    :chatSortDirection="props.chat_sort_direction" />
            </div>
            <div class="min-w-0 bg-cover flex flex-col chat-bg"
                :class="contact ? 'h-screen md:w-[70%]' : 'md:h-screen md:w-[70%]'">
                <ChatHeader v-if="contact" :ticketingIsEnabled="ticketingIsEnabled" :contact="contact"
                    :displayContactInfo="displayContactInfo" :ticket="ticket" :addon="addon"
                    @toggleView="toggleContactView" @deleteThread="deleteThread" @closeThread="closeThread" />
                <div v-if="contact && !displayTemplate" class="flex-1 overflow-y-auto" ref="scrollContainer2">
                    <ChatThread v-if="!displayContactInfo && !loadingThread && !displayTemplate" :contactId="contact.id"
                        :initialMessages="chatThread" :hasMoreMessages="hasMoreMessages" :initialNextPage="nextPage" />
                    <Contact v-if="displayContactInfo && !displayTemplate" class="bg-white h-full pb-8"
                        :fields="props.fields" :contact="contact" :locationSettings="props.locationSettings" />
                </div>
                <div v-if="contact && !displayContactInfo && !formLoading && !displayTemplate"
                    class="w-full py-4 mb-12">
                    <ChatForm :contact="contact" :simpleForm="simpleForm" :chatLimitReached="isChatLimitReached"
                        @viewTemplate="displayTemplate = true;" />
                </div>
                <div v-if="displayTemplate" class="flex-1 overflow-y-hidden">
                    <CampaignForm v-if="displayTemplate" class="bg-white h-full" :contact="contact.uuid"
                        :templates="templates" :contactGroups="[]" :settings="props.settings" :displayCancelBtn="false"
                        :displayTitle="true" :isCampaignFlow="false" :scheduleTemplate="false"
                        :sendText="'Send Message'" @viewTemplate="displayTemplate = false;" />
                </div>
            </div>
        </div>
        <button class="hidden" ref="toggleNavbarBtn" @click="slotProps.toggleNavBar"></button>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import { default as axios } from 'axios';
import { ref, onMounted, watch } from 'vue';
import CampaignForm from '@/Components/CampaignForm.vue';
import ChatForm from '@/Components/ChatComponents/ChatForm.vue';
import ChatHeader from '@/Components/ChatComponents/ChatHeader.vue';
import ChatTable from '@/Components/ChatComponents/ChatTable.vue';
import ChatThread from '@/Components/ChatComponents/ChatThread.vue';
import Contact from '@/Components/ContactInfo.vue';
import { getEchoInstance } from '../../../echo';

const props = defineProps({
    rows: Array,
    rowCount: Number,
    pusherSettings: Object,
    organizationId: Number,
    isChatLimitReached: Boolean,
    toggleNavBar: Function,
    state: String,
    demoNumber: String,
    settings: Object,
    status: String,
    chatThread: Array,
    hasMoreMessages: Boolean,
    nextPage: Number,
    addon: Object,
    contact: Object,
    ticket: Object,
    chat_sort_direction: String,
    filters: Object,
    templates: Array,
    fields: Array,
    locationSettings: Object,
    simpleForm: Boolean
});

const rows = ref(props.rows);
const scrollContainer2 = ref(null);
const loadingThread = ref(false);
const displayContactInfo = ref(false);
const displayTemplate = ref(false);
const formLoading = ref(false);
const isChatLimitReached = ref(props.isChatLimitReached);
const toggleNavbarBtn = ref(null);
const config = ref(props.settings.metadata);
const settings = ref(config.value ? JSON.parse(config.value) : null);
const ticketingIsEnabled = ref(settings.value?.tickets?.active ?? false);
const chatThread = ref(props.chatThread);
const contact = ref(props.contact);

watch(() => props.rows, (newRows) => {
    rows.value = newRows;
});

function toggleContactView(value) {
    displayContactInfo.value = value;
}

const scrollToBottom = () => {
    const container = scrollContainer2.value;
    if (container) {
        container.scrollTo({
            top: container.scrollHeight,
            behavior: 'smooth',
        });
    }
};

const closeThread = () => {
    toggleNavbarBtn.value.click();
    contact.value = null;
}

const deleteThread = async () => {
    chatThread.value = [];
    await axios.delete('/chats/' + contact.value.uuid);
}

const updateChatThread = (chat) => {
    const wamId = chat[0].value.wam_id;
    const wamIdExists = chatThread.value.some(existingChat => existingChat[0].value.wam_id === wamId);

    if (!wamIdExists && chat[0].value.deleted_at == null) {
        chatThread.value.push(chat);
        setTimeout(scrollToBottom, 100);
    }
}

const updateSidePanel = async (chat) => {
    if (contact.value && contact.value.id == chat[0].value.contact_id) {
        updateChatThread(chat);
    }

    try {
        const response = await axios.get('/chats');
        if (response?.data?.result) {
            rows.value = response.data.result;
        }
    } catch (error) {
        console.error('Error updating side panel:', error);
    }
}

onMounted(() => {
    const echo = getEchoInstance(
        props.pusherSettings['pusher_app_key'],
        props.pusherSettings['pusher_app_cluster']
    );

    echo.channel('chats.ch' + props.organizationId)
        .listen('NewChatEvent', (event) => {
            updateSidePanel(event.chat);
        });

    scrollToBottom();
});
</script> -->


<!-- ========================================= NEW UI CODE ==================================== -->

<template>
    <AppLayout v-slot:default="slotProps">
        <div class="md:h-[calc(100vh-65px)] md:flex md:flex-grow bg-gradient-to-br from-gray-50 to-orange-50/20">
            <!-- Chat List Sidebar -->
            <div class="md:w-[380px] md:flex flex-col bg-white shadow-xl border-r" :class="contact ? 'hidden' : ''">
                <ChatTable :rows="rows" :filters="props.filters" :rowCount="props.rowCount"
                    :ticketingIsEnabled="ticketingIsEnabled" :status="props?.status"
                    :chatSortDirection="props.chat_sort_direction" />
            </div>

            <!-- Main Chat Area -->
            <div class="flex-1 flex flex-col" :class="contact ? 'h-full' : 'md:h-full'">
                <ChatHeader v-if="contact" :ticketingIsEnabled="ticketingIsEnabled" :contact="contact"
                    :displayContactInfo="displayContactInfo" :ticket="ticket" :addon="addon"
                    @toggleView="toggleContactView" @deleteThread="deleteThread" @closeThread="closeThread" />

                <!-- Chat Messages Area -->
                <div v-if="contact && !displayTemplate" class="flex-1 overflow-y-auto bg-[#efeae2] chat-bg h-full"
                    ref="scrollContainer2">
                    <ChatThread v-if="!displayContactInfo && !loadingThread && !displayTemplate" :contactId="contact.id"
                        :initialMessages="chatThread" :hasMoreMessages="hasMoreMessages" :initialNextPage="nextPage" />
                    <Contact v-if="displayContactInfo && !displayTemplate" class="bg-white h-full pb-8"
                        :fields="props.fields" :contact="contact" :locationSettings="props.locationSettings" />
                </div>

                <!-- Chat Input Area -->
                <div v-if="contact && !displayContactInfo && !formLoading && !displayTemplate"
                    class="chat-bg border-t border-gray-200">
                    <ChatForm :contact="contact" :simpleForm="simpleForm" :chatLimitReached="isChatLimitReached"
                        @viewTemplate="displayTemplate = true;" />
                </div>

                <!-- Template View -->
                <div v-if="displayTemplate" class="flex-1 overflow-y-hidden bg-white">
                    <CampaignForm v-if="displayTemplate" class="h-full" :contact="contact.uuid" :templates="templates"
                        :contactGroups="[]" :settings="props.settings" :displayCancelBtn="false" :displayTitle="true"
                        :isCampaignFlow="false" :scheduleTemplate="false" :sendText="'Send Message'"
                        @viewTemplate="displayTemplate = false;" />
                </div>

                <!-- Empty State -->
                <div v-if="!contact" class="flex-1 flex items-center justify-center p-8">
                    <div class="text-center max-w-md">
                        <div class="mb-8 relative">
                            <div
                                class="w-32 h-32 mx-auto bg-gradient-to-br from-[#ff5100]/20 to-orange-100 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24"
                                    class="text-[#ff5100]">
                                    <path fill="currentColor"
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3s-3-1.34-3-3s1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22c.03-1.99 4-3.08 6-3.08c1.99 0 5.97 1.09 6 3.08c-1.29 1.94-3.5 3.22-6 3.22z" />
                                </svg>
                            </div>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-3">{{ $t('Select a conversation') }}</h2>
                        <p class="text-gray-500">{{ $t('Choose a chat from the list to start messaging') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <button class="hidden" ref="toggleNavbarBtn" @click="slotProps.toggleNavBar"></button>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import { default as axios } from 'axios';
import { ref, onMounted, watch } from 'vue';
import CampaignForm from '@/Components/CampaignForm.vue';
import ChatForm from '@/Components/ChatComponents/ChatForm.vue';
import ChatHeader from '@/Components/ChatComponents/ChatHeader.vue';
import ChatTable from '@/Components/ChatComponents/ChatTable.vue';
import ChatThread from '@/Components/ChatComponents/ChatThread.vue';
import Contact from '@/Components/ContactInfo.vue';
import { getEchoInstance } from '../../../echo';

const props = defineProps({
    rows: Array,
    rowCount: Number,
    pusherSettings: Object,
    organizationId: Number,
    isChatLimitReached: Boolean,
    toggleNavBar: Function,
    state: String,
    demoNumber: String,
    settings: Object,
    status: String,
    chatThread: Array,
    hasMoreMessages: Boolean,
    nextPage: Number,
    addon: Object,
    contact: Object,
    ticket: Object,
    chat_sort_direction: String,
    filters: Object,
    templates: Array,
    fields: Array,
    locationSettings: Object,
    simpleForm: Boolean
});

const rows = ref(props.rows);
const scrollContainer2 = ref(null);
const loadingThread = ref(false);
const displayContactInfo = ref(false);
const displayTemplate = ref(false);
const formLoading = ref(false);
const isChatLimitReached = ref(props.isChatLimitReached);
const toggleNavbarBtn = ref(null);
const config = ref(props.settings.metadata);
const settings = ref(config.value ? JSON.parse(config.value) : null);
const ticketingIsEnabled = ref(settings.value?.tickets?.active ?? false);
const chatThread = ref(props.chatThread);
const contact = ref(props.contact);

watch(() => props.rows, (newRows) => {
    rows.value = newRows;
});

function toggleContactView(value) {
    displayContactInfo.value = value;
}

const scrollToBottom = () => {
    const container = scrollContainer2.value;
    if (container) {
        container.scrollTo({
            top: container.scrollHeight,
            // behavior: 'smooth',
        });
    }
};

const closeThread = () => {
    toggleNavbarBtn.value.click();
    contact.value = null;
}

const deleteThread = async () => {
    chatThread.value = [];
    await axios.delete('/chats/' + contact.value.uuid);
}

const updateChatThread = (chat) => {
    const wamId = chat[0].value.wam_id;
    const wamIdExists = chatThread.value.some(existingChat => existingChat[0].value.wam_id === wamId);

    if (!wamIdExists && chat[0].value.deleted_at == null) {
        chatThread.value.push(chat);
        setTimeout(scrollToBottom, 100);
    }
}

const updateSidePanel = async (chat) => {
    if (contact.value && contact.value.id == chat[0].value.contact_id) {
        updateChatThread(chat);
    }

    try {
        const response = await axios.get('/chats');
        if (response?.data?.result) {
            rows.value = response.data.result;
        }
    } catch (error) {
        console.error('Error updating side panel:', error);
    }
}

onMounted(() => {
    const echo = getEchoInstance(
        props.pusherSettings['pusher_app_key'],
        props.pusherSettings['pusher_app_cluster']
    );

    echo.channel('chats.ch' + props.organizationId)
        .listen('NewChatEvent', (event) => {
            updateSidePanel(event.chat);
        });

    scrollToBottom();
});
</script>

<style scoped>
.chat-bg {
    background-color: #efeae2;
}
</style>