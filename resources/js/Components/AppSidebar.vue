<script setup lang="ts">
import { defineProps } from "vue";
import {
  Settings2,
  Boxes,
  LayoutDashboard,
  MessagesSquare,
  BookUser,
  Megaphone,
  FolderTree,
  Bot,
  Users,
  CreditCard,
  Handshake,
  CodeXml,
  type LucideIcon,
} from "lucide-vue-next"
import NavMain from './NavMain.vue'
import NavUser from './NavUser.vue'
import type { Component } from 'vue'
import TeamSwitcher from './TeamSwitcher.vue'

import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarRail,
} from '../Components/ui/sidebar'

import { computed } from 'vue'

type orgsName = { name: string; uuid: string }
type Org = { uuid: string; name: string; role?: string }
type Orgs = { uuid: string; organization: orgsName; role?: string }[]

type SidebarProps = {
  user: any
  organization?: Org | null
  organizations?: Orgs | null
  config?: any
  unreadMessages?: number
  collapsible?: 'icon' | 'full'
}

const props = withDefaults(defineProps<SidebarProps>(), {
  collapsible: 'icon',
})

const teamProps = computed(() => {
  const teams =
    props.organizations?.map((org) => ({
      id: org.uuid,
      uuid: org.organization?.uuid ?? '',
      name: org.organization?.name ?? '',
      logo: Boxes as Component,
      role: org.role,
    })) ?? []

  const activeTeam = {
    id: props.organization?.uuid ?? '',
    name: props.organization?.name ?? '',
    role:
      props.organizations?.find((item: any) => item?.organization?.uuid === props?.organization?.uuid)
        ?.role ?? ''
  }
  return { teams, activeTeam }
})

const user = computed(() => {
  return {
    id: props.user.id as number,
    first_name: props.user.first_name as string,
    last_name: props.user.last_name as string,
    full_name: props.user.full_name as string,
    email: props.user.email as string,
    avatar: props.user.avatar as string,
    phone: props.user.phone as string,
  }
});

type navItems = {
  title: string
  url: string
}

type NavMainType = {
  title: string;
  url: string;
  icon?: LucideIcon;
  items?: navItems[];
}

const data: { navMain: NavMainType[] } = {
  navMain: [
    {
      title: "Dashboard",
      url: "/dashboard",
      icon: LayoutDashboard,
    },
    {
      title: "Chats",
      url: "/chats",
      icon: MessagesSquare,
    },
    {
      title: "Contacts",
      url: "/contacts",
      icon: BookUser,
    },
    {
      title: "Campaigns",
      url: "/campaigns",
      icon: Megaphone,
    },
    {
      title: "Message Templates",
      url: "/templates",
      icon: FolderTree,
    },
    {
      title: "Automation",
      url: "/automation/basic",
      icon: Bot,
    },
    {
      title: "Team",
      url: "/team",
      icon: Users,
    },
    {
      title: "Settings",
      url: "/settings",
      icon: Settings2,
    },
    {
      title: "Billing & Subscription",
      url: "/billing",
      icon: CreditCard,
    },
    {
      title: "Support",
      url: "/support",
      icon: Handshake,
    },
    ...(props.user.teams[0]['role'] === 'owner' ? [{
      title: "Developer Tools",
      url: "/developer-tools/access-tokens",
      icon: CodeXml,
    }] : []),
  ],
}

</script>



<template>
  <Sidebar>
    <SidebarHeader>
      <TeamSwitcher v-bind="teamProps" />
    </SidebarHeader>
    <SidebarContent>
      <NavMain :items="data.navMain" />
    </SidebarContent>
    <SidebarFooter>
      <NavUser :user="user" />
    </SidebarFooter>
    <SidebarRail />
  </Sidebar>
</template>
