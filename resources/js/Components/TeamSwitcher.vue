<script setup lang="ts">
import { ref, type Component } from "vue"
import NyifeLogo from "../../images/nyife-logo.svg"
import { ChevronsUpDown, Plus } from "lucide-vue-next"
import OrganizationModal from '../Components/OrganizationModal.vue';

import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuShortcut,
  DropdownMenuTrigger,
} from '../Components/ui/dropdown-menu'

import {
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  useSidebar,
} from '../Components/ui/sidebar'
import { useForm } from "@inertiajs/vue3";


const props = defineProps<{
  teams: { id: string; uuid: string; name: string; logo: Component; role?: string }[];
  activeTeam: { id: string; name: string; role?: string };
}>();

const { isMobile } = useSidebar()
const isOpenOrganizationModal = ref(false);

const form: any = useForm({
  uuid: null,
})

const selectOrganization = (uuid: string | null) => {
  form.uuid = uuid;
  submitForm();
}


const submitForm = async () => {
  form.post('/select-organization', {
    preserveScroll: true,
    // onFinish: isLocationSwitchModalOpen.value = false,
  })
};

</script>

<template>
  <SidebarMenu>
    <SidebarMenuItem>
      <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <SidebarMenuButton size="lg"
            class="data-[state=open]:bg-sidebar-accent bg-black/5 hover:bg-black/10 data-[state=open]:text-sidebar-accent-foreground">
            <div class="flex aspect-square size-8 items-center justify-center rounded-lg bg-slate-200">
              <img :src="NyifeLogo" alt="Nyife Logo" class="size-8" />
            </div>
            <div class="grid flex-1 text-left text-sm leading-tight">
              <span class="truncate font-semibold">
                {{ props.activeTeam.name }}
              </span>
              <span class="truncate text-xs">{{ props.activeTeam?.role ?? 'Current'
                }}</span>
            </div>
            <ChevronsUpDown class="ml-auto" />
          </SidebarMenuButton>
        </DropdownMenuTrigger>
        <DropdownMenuContent class="w-[--reka-dropdown-menu-trigger-width] min-w-56 rounded-lg" align="start"
          :side="isMobile ? 'bottom' : 'right'" :side-offset="4">
          <DropdownMenuLabel class="text-xs text-muted-foreground">
            {{ $t('Organizations') }}
          </DropdownMenuLabel>
          <DropdownMenuItem v-for="(team, index) in props.teams" :key="team.name"
            @click.stop="selectOrganization(team.uuid)" class="gap-2 p-2 cursor-pointer"
            :class="props.activeTeam.id === team.uuid ? `bg-black/5` : ``">
            <div class="flex size-6 items-center justify-center rounded-sm border">
              <component :is="team.logo" class="size-4 shrink-0" />
            </div>
            <div class="grid flex-1 text-left text-sm leading-tight">{{
              team.name }}
              <span class="truncate text-xs">{{ team.role }}</span>
            </div>
            <DropdownMenuShortcut>⌘{{ index + 1 }}</DropdownMenuShortcut>
          </DropdownMenuItem>
          <DropdownMenuSeparator />
          <DropdownMenuItem class="gap-2 p-2 cursor-pointer" @click="isOpenOrganizationModal = true">
            <div class="flex size-6 items-center justify-center rounded-md border bg-background">
              <Plus class="size-4" />
            </div>
            {{ $t('Add Organization') }}
          </DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </SidebarMenuItem>
  </SidebarMenu>

  <OrganizationModal v-model:modelValue="isOpenOrganizationModal" />
</template>
