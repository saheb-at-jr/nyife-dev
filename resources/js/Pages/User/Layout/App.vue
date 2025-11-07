<!-- <template>
    <div :class="rtlClass">
        <MobileSidebar :user="user" :config="config" :organization="organization" :organizations="organizations"
            :title="currentPageTitle" :displayCreateBtn="displayCreateBtn" :displayTopBar="viewTopBar"></MobileSidebar>

        <div class="md:mt-0 md:pt-0 flex md:h-screen w-full tracking-[0.3px] bg-gray-300/10"
            :class="viewTopBar === false ? 'mt-0 pt-0' : ''">
            <Sidebar :user="user" :config="config" :organization="organization" :organizations="organizations"
                :unreadMessages="unreadMessages"></Sidebar>
            <div class="md:min-h-screen flex flex-col w-full min-w-0">
                <slot :user="user" :toggleNavBar="toggleTopBar" @testEmit="doSomething"></slot>
            </div>
        </div>

        <audio ref="audioPlayer" allow="autoplay"></audio>
    </div>
</template>
<script setup>
import { usePage } from "@inertiajs/vue3";
import Sidebar from "./Sidebar.vue";
import { ref, computed, watch, onMounted } from 'vue';
import { toast } from 'vue3-toastify';
import MobileSidebar from "./MobileSidebar.vue";
import 'vue3-toastify/dist/index.css';
import { getEchoInstance } from '../../../echo';
import { useRtl } from '../../../Composables/useRtl';

const { rtlClass } = useRtl();

const viewTopBar = ref(true);
const user = computed(() => usePage().props.auth.user);
const config = computed(() => usePage().props.config);
const organization = computed(() => usePage().props.organization);
const organizations = computed(() => usePage().props.organizations);
const currentPageTitle = computed(() => usePage().props.title);
const displayCreateBtn = computed(() => usePage().props.allowCreate);
const unreadMessages = ref(usePage().props.unreadMessages);
const audioPlayer = ref(null);

watch(() => [usePage().props.flash, { deep: true }], () => {
    if (usePage().props.flash.status != null) {
        toast(usePage().props.flash.status.message, {
            autoClose: 3000,
        });
    }
});

const toggleTopBar = () => {
    viewTopBar.value = !viewTopBar.value;
};

const getValueByKey = (key) => {
    const found = config.value.find(item => item.key === key);
    return found ? found.value : '';
};

const setupSound = () => {
    const settings = organization.value.metadata ? JSON.parse(organization.value.metadata) : {};
    const notifications = settings.notifications || {};

    if (notifications?.enable_sound && audioPlayer.value) {
        audioPlayer.value.src = notifications?.tone;
        audioPlayer.value.volume = notifications?.volume || 1.0;
    }
};

const playSound = () => {
    if (audioPlayer.value) {
        audioPlayer.value.play().catch((error) => {
            console.warn("Audio playback failed:", error);
        });
    }
};

onMounted(() => {
    setupSound();

    const echo = getEchoInstance(
        getValueByKey('pusher_app_key'),
        getValueByKey('pusher_app_cluster')
    );

    echo.channel('chats.ch' + organization.value.id).listen('NewChatEvent', (event) => {
        const chat = event.chat;

        if (chat[0].value.deleted_at == null && chat[0].value.type === 'inbound') {
            playSound(); // Play sound for inbound messages
            unreadMessages.value += 1; // Increment unread messages count
        }
    });
});
</script> -->

<!-- ======================= NEW APP LAYOUT WITH SIDEBAR ======================= -->

<script>
export const description
    = "A sidebar that collapses to icons."
export const iframeHeight = "800px"
export const containerClass = "w-full h-full"
</script>

<script setup>
import { usePage } from "@inertiajs/vue3";
import { ref, computed, watch, onMounted } from 'vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import { getEchoInstance } from '../../../echo';
import { useRtl } from '../../../Composables/useRtl';
import AppSidebar from '../../../Components/AppSidebar.vue'
import LangToggle from '../../../Components/LangToggle.vue';

// import {
//     Breadcrumb,
//     BreadcrumbItem,
//     BreadcrumbLink,
//     BreadcrumbList,
//     BreadcrumbPage,
//     BreadcrumbSeparator,
// } from '../../../Components/ui/breadcrumb'
import { Separator } from '../../../Components/ui/separator'
import {
    SidebarInset,
    SidebarProvider,
    SidebarTrigger,
} from '../../../Components/ui/sidebar'

const { rtlClass } = useRtl();

const viewTopBar = ref(true);
const user = computed(() => usePage().props.auth.user);
const config = computed(() => usePage().props.config);
const organization = computed(() => usePage().props.organization);
const organizations = computed(() => usePage().props.organizations);
// const currentPageTitle = computed(() => usePage().props.title);
// const displayCreateBtn = computed(() => usePage().props.allowCreate);
const unreadMessages = ref(usePage().props.unreadMessages);
const audioPlayer = ref(null);

watch(() => [usePage().props.flash, { deep: true }], () => {
    if (usePage().props.flash.status != null) {
        toast(usePage().props.flash.status.message, {
            autoClose: 3000,
        });
    }
});

const toggleTopBar = () => {
    viewTopBar.value = !viewTopBar.value;
};

const getValueByKey = (key) => {
    const found = config.value.find(item => item.key === key);
    return found ? found.value : '';
};

const setupSound = () => {
    const settings = organization.value.metadata ? JSON.parse(organization.value.metadata) : {};
    const notifications = settings.notifications || {};

    if (notifications?.enable_sound && audioPlayer.value) {
        audioPlayer.value.src = notifications?.tone;
        audioPlayer.value.volume = notifications?.volume || 1.0;
    }
};

const playSound = () => {
    if (audioPlayer.value) {
        audioPlayer.value.play().catch((error) => {
            console.warn("Audio playback failed:", error);
        });
    }
};

onMounted(() => {
    setupSound();

    const echo = getEchoInstance(
        getValueByKey('pusher_app_key'),
        getValueByKey('pusher_app_cluster')
    );

    echo.channel('chats.ch' + organization.value.id).listen('NewChatEvent', (event) => {
        const chat = event.chat;

        if (chat[0].value.deleted_at == null && chat[0].value.type === 'inbound') {
            playSound(); // Play sound for inbound messages
            unreadMessages.value += 1; // Increment unread messages count
        }
    });
});

const languages = computed(() => usePage().props.languages);
const currentLanguage = computed(() => usePage().props.currentLanguage);

</script>

<template>
    <SidebarProvider :class="rtlClass">
        <AppSidebar :user="user" :config="config" :organization="organization" :organizations="organizations"
            :unreadMessages="unreadMessages" />
        <SidebarInset>
            <header
                class="w-full flex h-16 shrink-0 justify-between items-center gap-2 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12">
                <div class="flex items-center gap-2 px-4">
                    <SidebarTrigger class="-ml-1" />
                    <!-- <Separator orientation="vertical" class="mr-2 data-[orientation=vertical]:h-4" />
                    <Breadcrumb>
                        <BreadcrumbList>
                            <BreadcrumbItem class="hidden md:block">
                                <BreadcrumbLink href="#">
                                    Building Your Application
                                </BreadcrumbLink>
                            </BreadcrumbItem>
                            <BreadcrumbSeparator class="hidden md:block" />
                            <BreadcrumbItem>
                                <BreadcrumbPage>Data Fetching</BreadcrumbPage>
                            </BreadcrumbItem>
                        </BreadcrumbList>
                    </Breadcrumb> -->
                </div>
                <div class="mr-8 flex items-center gap-4 bg-slate-100 rounded-3xl py-1 px-2"
                    :class="menuIconsOnly ? 'hidden' : ''">
                    <LangToggle class="text-black" :languages="languages" :currentLanguage="currentLanguage" />
                </div>
            </header>
            <Separator orientation="horizontal" />
            <div class="flex flex-1 flex-col gap-4 pt-0 w-full">
                <slot :user="user" :toggleNavBar="toggleTopBar" @testEmit="doSomething"></slot>
            </div>
        </SidebarInset>
        <audio ref="audioPlayer" allow="autoplay"></audio>
    </SidebarProvider>
</template>
