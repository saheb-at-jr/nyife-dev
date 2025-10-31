<script setup lang="ts">
import {
  ChevronsUpDown,
  CreditCard,
  LogOut,
  Settings,
  SquareArrowOutUpRight,
  User,
} from "lucide-vue-next"

import {
  Avatar,
  AvatarFallback,
  AvatarImage,
} from '../Components/ui/avatar'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '../Components/ui/dropdown-menu'
import {
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  useSidebar,
} from '../Components/ui/sidebar'
import ProfileModal from '../Components/ProfileModal.vue';
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";


const props = defineProps<{
  user: { id: any; first_name: string; last_name?: string; full_name?: string; email?: string; avatar?: string; phone?: string }
}>()

const { isMobile } = useSidebar()
const isOpen = ref(false);

const closeModal = () => {
  isOpen.value = false
}

const openModal = () => {
  isOpen.value = true;
}
</script>

<template>
  <SidebarMenu>
    <SidebarMenuItem>
      <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <SidebarMenuButton size="lg"
            class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground bg-black/5 hover:bg-black/10">
            <Avatar class="h-8 w-8 rounded-lg">
              <AvatarImage v-if="props.user.avatar" :src="props.user.avatar"
                :alt="props.user.full_name || props.user.first_name" />
              <AvatarFallback v-else class="rounded-lg">
                {{ props.user.full_name?.at(0)?.toUpperCase() || props.user.first_name?.at(1)?.toUpperCase() }}
              </AvatarFallback>
            </Avatar>
            <div class="grid flex-1 text-left text-sm leading-tight">
              <span class="truncate font-semibold">{{ props.user.full_name || props.user.first_name }}</span>
              <span class="truncate text-xs">{{ props.user.email }}</span>
            </div>
            <ChevronsUpDown class="ml-auto size-4" />
          </SidebarMenuButton>
        </DropdownMenuTrigger>
        <DropdownMenuContent class="w-[--reka-dropdown-menu-trigger-width] min-w-56 rounded-lg"
          :side="isMobile ? 'bottom' : 'right'" align="end" :side-offset="4">
          <DropdownMenuLabel class="p-0 font-normal">
            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
              <Avatar class="h-8 w-8 rounded-lg">
                <AvatarImage v-if="props.user.avatar" :src="props.user.avatar"
                  :alt="props.user.full_name || props.user.first_name" />
                <AvatarFallback v-else class="rounded-lg">
                  {{ props.user.full_name?.at(0)?.toUpperCase() || props.user.first_name?.at(1)?.toUpperCase() }}
                </AvatarFallback>
              </Avatar>
              <div class="grid flex-1 text-left text-sm leading-tight">
                <span class="truncate font-semibold">{{ props.user.full_name || props.user.first_name }}</span>
                <span class="truncate text-xs">{{ props.user.email }}</span>
              </div>
            </div>
          </DropdownMenuLabel>
          <DropdownMenuSeparator />
          <DropdownMenuGroup>
          </DropdownMenuGroup>
          <DropdownMenuSeparator />
          <DropdownMenuGroup>
            <DropdownMenuItem @click="openModal" class="cursor-pointer">
              <User />
              {{ $t('View Profile') }}
            </DropdownMenuItem>
            <Link href="/billing" class="w-full">
            <DropdownMenuItem class="cursor-pointer">
              <CreditCard />
              {{ $t('Billing & Subscription') }}
            </DropdownMenuItem>
            </Link>

            <Link href="/settings" class="w-full">
            <DropdownMenuItem class="cursor-pointer">
              <Settings />
              {{ $t('Settings') }}
            </DropdownMenuItem>
            </Link>
            <a href="https://pinex.pinnacle.in/cm/#/login" target="_blank" rel="noopener noreferrer">
              <DropdownMenuItem class="cursor-pointer">
                <SquareArrowOutUpRight />
                {{ $t('Login for SMS Account') }}
              </DropdownMenuItem>
            </a>

          </DropdownMenuGroup>
          <DropdownMenuSeparator />
          <Link href="/logout" class="w-full">
          <DropdownMenuItem class="cursor-pointer">
            <LogOut />
            {{ $t('Log out') }}
          </DropdownMenuItem>
          </Link>
        </DropdownMenuContent>
      </DropdownMenu>
    </SidebarMenuItem>
  </SidebarMenu>
  <ProfileModal :user="props.user" :isOpen="isOpen" role="user" @close="closeModal()" />
</template>
