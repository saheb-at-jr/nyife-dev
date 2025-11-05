<template>
  <AppLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-orange-50 to-gray-50 p-4 md:p-8">

      <!-- Top Header Bar -->
      <div class="mb-8 border-2 border-primary/10 rounded-2xl p-6 bg-white shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <div class="flex items-center gap-3 mb-2">
              <div
                class="w-12 h-12 bg-primary rounded-2xl flex items-center justify-center shadow-lg shadow-primary/30">
                <LayoutDashboard class="w-6 h-6 text-white" />
              </div>
              <div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900">
                  {{ $t("Welcome backs") }}, {{ props.auth.user.first_name }} 👋
                </h1>
                <p class="text-gray-600 text-sm">{{ $t("Dashboard") }}</p>
              </div>
            </div>
          </div>

          <!-- Wallet Section - Compact Top Right -->
          <div class="flex items-center gap-3">
            <div class="relative group">
              <div
                class="relative bg-white border border-primary/20 rounded-2xl px-6 py-4 flex items-center gap-4 shadow-lg">
                <div class="bg-gradient-to-br from-amber-400 to-amber-500 rounded-xl p-2.5 shadow-md">
                  <Wallet class="w-5 h-5 text-white" />
                </div>
                <div>
                  <p class="text-gray-600 text-xs font-medium">{{ $t("Wallet Balance") }}</p>
                  <p class="text-gray-900 text-xl font-bold">₹{{ props.auth.user.balance }}</p>
                </div>
                <button @click="openHistoryModal(props.auth.user.id)"
                  class="ml-2 bg-orange-50 hover:bg-orange-100 rounded-lg p-2 transition-all duration-300 hover:scale-110 group/btn">
                  <History class="w-4 h-4 text-primary group-hover/btn:text-primary" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Warning Alert -->
        <div class="mt-4 flex items-start gap-3 bg-red-50 border border-red-200 rounded-2xl px-5 py-3.5 shadow-sm">
          <div class="bg-red-100 rounded-lg p-2 flex-shrink-0">
            <AlertCircle class="w-4 h-4 text-red-600" />
          </div>
          <p class="text-red-700 text-sm leading-relaxed">
            <span class="font-semibold">Important:</span> If your wallet balance is less than ₹1 or if your campaign
            cost exceeds your available balance, you will not be able to send messages.
          </p>
        </div>
      </div>

      <!-- Quick Actions Bar -->
      <div class="mb-8">
        <div class="relative">
          <div class="absolute inset-0 bg-gradient-to-r from-orange-50 to-gray-50 rounded-3xl blur-xl"></div>
          <div class="relative bg-white/80 backdrop-blur-sm border border-gray-200 rounded-3xl p-6 shadow-xl">
            <div class="flex items-center gap-2 mb-4">
              <Zap class="w-5 h-5 text-amber-500" />
              <h3 class="text-gray-900 font-semibold text-lg">Quick Actions</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <Link href="/contacts/add"
                class="group relative overflow-hidden bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-5 hover:shadow-xl hover:shadow-blue-500/30 transition-all duration-300 hover:scale-[1.02]">
              <div class="absolute top-0 right-0 w-32 h-32 bg-white/20 rounded-full blur-2xl"></div>
              <div class="relative flex items-center justify-between">
                <div>
                  <div class="bg-white/30 backdrop-blur rounded-xl p-2.5 w-fit mb-3">
                    <UserPlus class="w-5 h-5 text-white" />
                  </div>
                  <h4 class="text-white font-bold text-lg mb-1">{{ $t("Add contact") }}</h4>
                  <p class="text-blue-50 text-sm">Create new contact entry</p>
                </div>
                <ArrowUpRight
                  class="w-5 h-5 text-white/60 group-hover:text-white group-hover:translate-x-1 group-hover:-translate-y-1 transition-all" />
              </div>
              </Link>

              <Link href="/campaigns/create"
                class="group relative overflow-hidden bg-primary rounded-2xl p-5 hover:shadow-xl hover:shadow-primary/30 transition-all duration-300 hover:scale-[1.02]">
              <div class="absolute top-0 right-0 w-32 h-32 bg-white/20 rounded-full blur-2xl"></div>
              <div class="relative flex items-center justify-between">
                <div>
                  <div class="bg-white/30 backdrop-blur rounded-xl p-2.5 w-fit mb-3">
                    <Rocket class="w-5 h-5 text-white" />
                  </div>
                  <h4 class="text-white font-bold text-lg mb-1">{{ $t("Create campaign") }}</h4>
                  <p class="text-orange-50 text-sm">Launch new campaign</p>
                </div>
                <ArrowUpRight
                  class="w-5 h-5 text-white/60 group-hover:text-white group-hover:translate-x-1 group-hover:-translate-y-1 transition-all" />
              </div>
              </Link>

              <Link href="/templates/create"
                class="group relative overflow-hidden bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-5 hover:shadow-xl hover:shadow-purple-500/30 transition-all duration-300 hover:scale-[1.02]">
              <div class="absolute top-0 right-0 w-32 h-32 bg-white/20 rounded-full blur-2xl"></div>
              <div class="relative flex items-center justify-between">
                <div>
                  <div class="bg-white/30 backdrop-blur rounded-xl p-2.5 w-fit mb-3">
                    <FileText class="w-5 h-5 text-white" />
                  </div>
                  <h4 class="text-white font-bold text-lg mb-1">{{ $t("Create template") }}</h4>
                  <p class="text-purple-50 text-sm">Design message template</p>
                </div>
                <ArrowUpRight
                  class="w-5 h-5 text-white/60 group-hover:text-white group-hover:translate-x-1 group-hover:-translate-y-1 transition-all" />
              </div>
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Dashboard -->
      <div class="flex items-center gap-2 mb-4">
        <ChartNoAxesCombined class="w-5 h-5 text-green-500" />
        <h3 class="text-gray-900 font-semibold text-lg">Stats</h3>
      </div>
      <!-- Stats Dashboard -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <!-- Contacts -->
        <div class="group relative">
          <div
            class="absolute -inset-0.5 bg-gradient-to-r from-blue-400 to-cyan-400 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
          </div>
          <div
            class="relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-blue-300 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/10">
            <div class="flex justify-between items-start mb-4">
              <div class="bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl p-3 shadow-md">
                <Users class="w-6 h-6 text-white" />
              </div>
              <div class="flex items-center gap-1 bg-emerald-50 rounded-lg px-2 py-1 border border-emerald-200">
                <TrendingUp class="w-3 h-3 text-emerald-600" />
              </div>
            </div>
            <p class="text-gray-600 text-sm font-medium mb-1">{{ $t("Contacts") }}</p>
            <p class="text-gray-900 text-3xl font-bold mb-3">{{ props.contactCount }}</p>
            <Link href="/contacts"
              class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 text-sm font-medium group-hover:gap-2 transition-all">
            <span>{{ $t("View contacts") }}</span>
            <ArrowRight class="w-4 h-4" />
            </Link>
          </div>
        </div>

        <!-- Campaigns -->
        <div class="group relative">
          <div class="absolute -inset-0.5 bg-primary rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
          </div>
          <div
            class="relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-orange-300 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-primary/10">
            <div class="flex justify-between items-start mb-4">
              <div class="bg-primary rounded-xl p-3 shadow-md shadow-primary/30">
                <Megaphone class="w-6 h-6 text-white" />
              </div>
              <div class="flex items-center gap-1 bg-emerald-50 rounded-lg px-2 py-1 border border-emerald-200">
                <TrendingUp class="w-3 h-3 text-emerald-600" />
              </div>
            </div>
            <p class="text-gray-600 text-sm font-medium mb-1">{{ $t("Campaigns") }}</p>
            <p class="text-gray-900 text-3xl font-bold mb-3">{{ props.campaignCount }}</p>
            <Link href="/campaigns"
              class="inline-flex items-center gap-1 text-primary hover:text-primary text-sm font-medium group-hover:gap-2 transition-all">
            <span>{{ $t("View campaigns") }}</span>
            <ArrowRight class="w-4 h-4" />
            </Link>
          </div>
        </div>

        <!-- Templates -->
        <div class="group relative">
          <div
            class="absolute -inset-0.5 bg-gradient-to-r from-purple-400 to-pink-400 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
          </div>
          <div
            class="relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-purple-300 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-purple-500/10">
            <div class="flex justify-between items-start mb-4">
              <div class="bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl p-3 shadow-md">
                <FileText class="w-6 h-6 text-white" />
              </div>
              <div class="flex items-center gap-1 bg-emerald-50 rounded-lg px-2 py-1 border border-emerald-200">
                <TrendingUp class="w-3 h-3 text-emerald-600" />
              </div>
            </div>
            <p class="text-gray-600 text-sm font-medium mb-1">{{ $t("Templates") }}</p>
            <p class="text-gray-900 text-3xl font-bold mb-3">{{ props.templateCount }}</p>
            <Link href="/templates"
              class="inline-flex items-center gap-1 text-purple-600 hover:text-purple-700 text-sm font-medium group-hover:gap-2 transition-all">
            <span>{{ $t("View templates") }}</span>
            <ArrowRight class="w-4 h-4" />
            </Link>
          </div>
        </div>

        <!-- Chats -->
        <div class="group relative col-span-2 lg:col-span-1">
          <div
            class="absolute -inset-0.5 bg-gradient-to-r from-emerald-400 to-green-400 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
          </div>
          <div
            class="relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-emerald-300 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-emerald-500/10">
            <div class="flex justify-between items-start mb-4">
              <div class="bg-gradient-to-br from-emerald-500 to-green-500 rounded-xl p-3 shadow-md">
                <MessageSquare class="w-6 h-6 text-white" />
              </div>
              <div class="flex items-center gap-1 bg-emerald-50 rounded-lg px-2 py-1 border border-emerald-200">
                <TrendingUp class="w-3 h-3 text-emerald-600" />
              </div>
            </div>
            <p class="text-gray-600 text-sm font-medium mb-1">{{ $t("All chats") }}</p>
            <p class="text-gray-900 text-3xl font-bold mb-3">{{ props.chatCount }}</p>
            <Link href="/chats"
              class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-700 text-sm font-medium group-hover:gap-2 transition-all">
            <span>{{ $t("View chats") }}</span>
            <ArrowRight class="w-4 h-4" />
            </Link>
          </div>
        </div>
      </div>

      <!-- Main Content Area -->
      <div class="grid lg:grid-cols-3 gap-6">

        <!-- Left Column - Chart (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Analytics Chart -->
          <div class="relative group">
            <div
              class="absolute -inset-0.5 bg-gradient-to-r from-primary/30 to-blue-400/30 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
            </div>
            <div class="relative bg-white border border-gray-200 rounded-2xl p-6 shadow-lg">
              <div class="flex items-center justify-between mb-6">
                <div>
                  <h3 class="text-gray-900 text-xl font-bold mb-1">Chat Analytics</h3>
                  <p class="text-gray-600 text-sm">Track your messaging performance</p>
                </div>
                <div class="bg-primary rounded-xl p-3 shadow-md shadow-primary/30">
                  <Activity class="w-6 h-6 text-white" />
                </div>
              </div>
              <div id="chart">
                <apexchart type="area" height="350" :options="chartOptions" :series="series"></apexchart>
              </div>
            </div>
          </div>

          <!-- Campaigns List -->
          <div class="relative group">
            <div
              class="absolute -inset-0.5 bg-gradient-to-r from-pink-300 to-rose-300 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
            </div>
            <div class="relative bg-white border border-gray-200 rounded-2xl p-6 shadow-lg">
              <div class="flex items-center justify-between mb-6">
                <div>
                  <h3 class="text-gray-900 text-xl font-bold mb-1">{{ $t("Campaigns") }}</h3>
                  <p class="text-gray-600 text-sm">{{ $t("Below are your outgoing or scheduled campaigns") }}</p>
                </div>
                <div class="bg-gradient-to-br from-pink-500 to-rose-500 rounded-xl p-3 shadow-md">
                  <Send class="w-6 h-6 text-white" />
                </div>
              </div>

              <div v-if="props.campaigns.length === 0"
                class="border-2 border-dashed border-gray-300 rounded-2xl p-10 text-center bg-gray-50">
                <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                  <Inbox class="w-10 h-10 text-gray-400" />
                </div>
                <p class="text-gray-600 text-sm mb-4">{{ $t("You do not have any outgoing or scheduled campaigns") }}
                </p>
                <Link href="/campaigns/create"
                  class="inline-flex items-center gap-2 bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white font-semibold px-6 py-3 rounded-xl transition-all duration-300 hover:scale-105 shadow-lg">
                <Plus class="w-4 h-4" />
                <span>{{ $t("Create campaign") }}</span>
                </Link>
              </div>

              <div v-else class="space-y-3">
                <div v-for="(item, index) in props.campaigns" :key="index"
                  class="group/item relative overflow-hidden bg-gradient-to-r from-orange-50 to-gray-50 hover:from-orange-100 hover:to-gray-100 border border-gray-200 hover:border-orange-300 rounded-xl p-4 transition-all duration-300">
                  <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-pink-500 to-rose-500"></div>
                  <div class="flex items-center justify-between pl-3">
                    <div class="flex items-center gap-4">
                      <div class="bg-pink-100 border border-pink-200 rounded-lg p-2.5">
                        <Send class="w-4 h-4 text-pink-600" />
                      </div>
                      <span class="text-gray-900 font-medium capitalize">{{ item.name }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                      <span
                        class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-700 text-xs font-semibold px-3 py-1.5 rounded-full capitalize border border-amber-200">
                        <div class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></div>
                        {{ item.status }}
                      </span>
                      <ChevronRight
                        class="w-4 h-4 text-gray-400 opacity-0 group-hover/item:opacity-100 transition-opacity" />
                    </div>
                  </div>
                </div>

                <Link href="/campaigns"
                  class="inline-flex items-center gap-2 text-sm text-pink-600 hover:text-pink-700 font-medium mt-4 group/link">
                <span>{{ $t("View more campaigns") }}</span>
                <ArrowRight class="w-4 h-4 group-hover/link:translate-x-1 transition-transform" />
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column - Status Cards (1/3 width) -->
        <div class="lg:col-span-1 space-y-6">

          <!-- Subscription Status -->
          <div class="relative group">
            <div class="absolute -inset-0.5 rounded-2xl blur opacity-40 group-hover:opacity-60 transition"
              :class="!subscriptionIsActive ? 'bg-gradient-to-r from-red-400 to-rose-400' : 'bg-gradient-to-r from-emerald-400 to-green-400'">
            </div>
            <div class="relative rounded-2xl overflow-hidden shadow-lg"
              :class="!subscriptionIsActive ? 'bg-gradient-to-br from-red-500 to-rose-600' : 'bg-gradient-to-br from-emerald-500 to-green-600'">
              <div class="absolute inset-0 bg-white/10"></div>
              <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/20 rounded-full blur-3xl"></div>
              <div class="relative p-6">
                <div class="flex items-start justify-between mb-4">
                  <div
                    class="flex items-center gap-2 bg-white/30 backdrop-blur rounded-full px-3 py-1.5 border border-white/40">
                    <div class="w-2 h-2 rounded-full animate-pulse"
                      :class="subscriptionIsActive ? 'bg-emerald-100' : 'bg-red-100'"></div>
                    <span class="text-white text-xs font-bold uppercase tracking-wide">
                      {{ subscriptionIsActive ? 'ACTIVE' : 'INACTIVE' }}
                    </span>
                  </div>
                  <div class="bg-white/30 backdrop-blur rounded-xl p-2.5 border border-white/40">
                    <Shield v-if="subscriptionIsActive" class="w-5 h-5 text-white" />
                    <ShieldAlert v-else class="w-5 h-5 text-white" />
                  </div>
                </div>

                <h3 class="text-white text-lg font-bold mb-2">
                  <span v-if="props.subscription?.status === 'trial' && !subscriptionIsActive">
                    {{ $t("Your trial period is over") }}
                  </span>
                  <span v-if="props.subscription?.status === 'active' && !subscriptionIsActive">
                    {{ $t("We were unable to autorenew your subscription") }}
                  </span>
                  <span v-if="props.subscription?.status === 'active' && subscriptionIsActive">
                    {{ $t("Active subscription") }}
                  </span>
                  <span v-if="props.subscription?.status === 'trial' && subscriptionIsActive">
                    {{ $t("Trial period") }}
                  </span>
                </h3>

                <p class="text-white/95 text-sm mb-4 leading-relaxed">
                  <span v-if="props.subscription?.status === 'trial' && !subscriptionIsActive">
                    {{ $t("Please subscribe to a plan to continue using the app") }}
                  </span>
                  <span v-if="props.subscription?.status === 'active' && !subscriptionIsActive">
                    {{ $t("To continue using the app, please make a payment of") }}
                    {{ props.subscriptionDetails?.accountBalance }}.
                  </span>
                  <span v-if="props.subscription?.status === 'trial' && subscriptionIsActive">
                    {{ $t("Your trial period expires on") }}
                    {{ props.subscription?.valid_until }}
                  </span>
                  <span v-if="props.subscription?.status === 'active' && subscriptionIsActive">
                    {{ $t("Your subscription expires on") }}
                    {{ props.subscription?.valid_until }}
                  </span>
                </p>

                <Link v-if="props.auth.user.teams[0].role === 'owner' && props.subscription?.status === 'trial'"
                  href="/subscription"
                  class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-900 font-bold px-5 py-2.5 rounded-xl transition-all duration-300 hover:scale-105 shadow-xl">
                <Zap class="w-4 h-4" />
                <span>{{ $t("Subscribe") }}</span>
                </Link>

                <Link
                  v-if="props.auth.user.teams[0].role === 'owner' && props.subscription?.status === 'active' && !subscriptionIsActive"
                  href="/billing"
                  class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-900 font-bold px-5 py-2.5 rounded-xl transition-all duration-300 hover:scale-105 shadow-xl">
                <CreditCard class="w-4 h-4" />
                <span>{{ $t("Add payment") }}</span>
                </Link>
              </div>
            </div>
          </div>

          <!-- Setup WhatsApp -->
          <div v-if="props.auth.user.teams[0].role === 'owner' && props.setupWhatsapp === true" class="relative group">
            <div
              class="absolute -inset-0.5 bg-gradient-to-r from-green-400 to-emerald-400 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
            </div>
            <div class="relative bg-white border border-gray-200 rounded-2xl p-6 shadow-lg">
              <div class="flex items-start justify-between mb-4">
                <div class="bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl p-3 shadow-md">
                  <MessageCircle class="w-6 h-6 text-white" />
                </div>
                <div class="bg-amber-100 border border-amber-200 rounded-full p-1.5">
                  <AlertCircle class="w-4 h-4 text-amber-600" />
                </div>
              </div>
              <h3 class="text-gray-900 text-lg font-bold mb-2">{{ $t("Setup whatsapp") }}</h3>
              <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                {{ $t("Setup your whatsapp API to start using your instance") }}
              </p>

              <EmbeddedSignupBtn v-if="embeddedSignupActive == 1" :appId="props.appId" :configId="props.configId"
                :graphAPIVersion="props.graphAPIVersion" />

              <Link v-else href="/settings/whatsapp"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white font-semibold px-5 py-2.5 rounded-xl transition-all duration-300 hover:scale-105 shadow-lg">
              <span>{{ $t("Setup whatsapp") }}</span>
              <ArrowRight class="w-4 h-4" />
              </Link>
            </div>
          </div>

          <!-- Team Setup -->
          <div v-if="props.auth.user.teams[0].role === 'owner' && displayTeamNotification() === true"
            class="relative group">
            <div
              class="absolute -inset-0.5 bg-gradient-to-r from-amber-400 to-orange-400 rounded-2xl blur opacity-20 group-hover:opacity-40 transition">
            </div>
            <div class="relative bg-white border border-gray-200 rounded-2xl p-6 shadow-lg">
              <div class="flex items-start justify-between mb-4">
                <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl p-3 shadow-md">
                  <UsersRound class="w-6 h-6 text-white" />
                </div>
              </div>
              <h3 class="text-gray-900 text-lg font-bold mb-2">{{ $t("Setup team") }}</h3>
              <p class="text-gray-600 text-sm mb-4">{{ $t("Add other people to your team") }}</p>

              <div class="flex flex-col gap-3">
                <Link href="team"
                  class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold px-5 py-2.5 rounded-xl transition-all duration-300 hover:scale-105 shadow-lg">
                <UserPlus class="w-4 h-4" />
                <span>{{ $t("Add users") }}</span>
                </Link>

                <button @click="dismissNotification()"
                  class="text-sm text-gray-600 hover:text-gray-700 underline transition-colors">
                  {{ $t("Dismiss notification") }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Balance History Modal -->
      <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100" leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
        <div v-if="isHistoryModalOpen"
          class="fixed inset-0 flex items-center justify-center bg-black/60 backdrop-blur-sm z-50 p-4"
          @click.self="closeHistoryModal">
          <div class="relative w-full max-w-6xl max-h-[90vh] flex flex-col">
            <!-- Glow Effect -->
            <div class="absolute -inset-1 bg-gradient-to-r from-primary/40 to-blue-400 rounded-3xl blur-xl opacity-30">
            </div>

            <!-- Modal Content -->
            <div
              class="relative bg-white border border-primary/20 rounded-3xl shadow-2xl flex flex-col overflow-hidden">
              <!-- Modal Header -->
              <div class="relative overflow-hidden px-8 py-6 border-b border-primary/20">
                <div class="relative flex items-center justify-between">
                  <div class="flex items-center gap-4">
                    <div class="rounded-2xl p-3.5 shadow-lg shadow-primary/30">
                      <History class="w-7 h-7 text-black" />
                    </div>
                    <div>
                      <h2 class="text-2xl font-bold text-black">{{ $t('Balance History') }}</h2>
                      <p class="text-black/60 text-sm">Complete transaction records</p>
                    </div>
                  </div>
                  <div className="flex items-center justify-between">
                    <p class="text-gray-900 text-xl font-bold rounded-xl bg-black/5 py-1 px-3 mr-4">₹{{
                      props.auth.user.balance }}
                    </p>
                    <button @click="closeHistoryModal"
                      class="text-primary/60 hover:text-primary/70 hover:bg-primary/10 rounded-xl p-2.5 transition-all duration-300 hover:rotate-90">
                      <X class="w-6 h-6" />
                    </button>
                  </div>
                </div>
              </div>

              <!-- Modal Body -->
              <div class="flex-1 overflow-y-auto p-6">
                <div class="rounded-2xl border border-primary/10 overflow-hidden">
                  <div class="overflow-x-auto">
                    <table class="w-full">
                      <thead>
                        <tr class="bg-primary/10 border-b border-primary/20">
                          <th class="px-6 py-4 text-left text-sm font-bold text-black/90 uppercase tracking-wider">
                            {{ $t('Date') }}
                          </th>
                          <th class="px-6 py-4 text-left text-sm font-bold text-black/90 uppercase tracking-wider">
                            {{ $t('Amount') }}
                          </th>
                          <th class="px-6 py-4 text-left text-sm font-bold text-black/90 uppercase tracking-wider">
                            {{ $t('Balance After') }}
                          </th>
                          <th class="px-6 py-4 text-left text-sm font-bold text-black/90 uppercase tracking-wider">
                            {{ $t('Type') }}
                          </th>
                          <th class="px-6 py-4 text-left text-sm font-bold text-black/90 uppercase tracking-wider">
                            {{ $t('Note') }}
                          </th>
                        </tr>
                      </thead>
                      <tbody class="bg-white">
                        <tr v-for="record in balanceHistory" :key="record.id"
                          class="hover:bg-black/5 transition-colors duration-200 group">
                          <td class="px-6 py-4 text-sm whitespace-nowrap">
                            <div class="flex items-center gap-2">
                              <Calendar class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" />
                              {{ new Date(record.created_at).toLocaleString() }}
                            </div>
                          </td>
                          <td class="px-6 py-4 text-sm font-bold whitespace-nowrap"
                            :class="record.type === 'credit' ? 'text-emerald-600' : 'text-red-600'">
                            <div class="flex items-center gap-2">
                              <ArrowUpCircle v-if="record.type === 'credit'" class="w-4 h-4" />
                              <ArrowDownCircle v-else class="w-4 h-4" />
                              {{ record.type === 'credit' ? '+' : '-' }}₹{{ record.amount }}
                            </div>
                          </td>
                          <td class="px-6 py-4 text-sm text-black/90 font-semibold whitespace-nowrap">
                            ₹{{ record.balance_after }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <span
                              class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold capitalize"
                              :class="record.type === 'credit'
                                ? 'bg-emerald-100 text-emerald-700 border border-emerald-200'
                                : 'bg-red-100 text-red-700 border border-red-200'">
                              <div class="w-2 h-2 rounded-full animate-pulse"
                                :class="record.type === 'credit' ? 'bg-emerald-500' : 'bg-red-500'">
                              </div>
                              {{ record.type }}
                            </span>
                          </td>
                          <td class="px-6 py-4 text-sm">
                            {{ record.note ?? '-' }}
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <!-- Modal Footer -->
              <div class="relative overflow-hidden px-8 py-6 border-t border-primary/10">
                <div class="relative flex justify-end gap-4">
                  <button @click="router.visit('/billing')"
                    class="inline-flex items-center gap-2 bg-green-500/80 hover:bg-green-500 text-white font-bold px-6 py-3 rounded-xl transition-all duration-300 hover:scale-105 shadow-lg">
                    <PlusIcon class="w-5 h-5" />
                    <span>{{ $t('Add Payment') }}</span>
                  </button>
                  <button @click="closeHistoryModal"
                    class="inline-flex items-center gap-2 bg-primary/80 hover:bg-primary text-white font-bold px-6 py-3 rounded-xl transition-all duration-300 hover:scale-105 shadow-lg">
                    <Check class="w-5 h-5" />
                    <span>{{ $t('Close') }}</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </Transition>

    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from "./Layout/App.vue";
import { Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import { trans } from "laravel-vue-i18n";
import EmbeddedSignupBtn from "@/Components/EmbeddedSignupBtn.vue";
import axios from 'axios';
import {
  Wallet, History, AlertCircle, UserPlus, Send, FileText,
  ArrowRight, Users, TrendingUp, Megaphone, MessageSquare,
  Activity, Zap, CreditCard, Shield, ShieldAlert, MessageCircle,
  UsersRound, Rocket, Inbox, Plus, X, Check, LayoutDashboard,
  ArrowUpRight, Calendar, ArrowUpCircle, ArrowDownCircle, ChevronRight,
  PlusIcon,
  ChartNoAxesCombined
} from 'lucide-vue-next';

const props = defineProps({
  user: Object,
  auth: Object,
  subscription: Object,
  subscriptionIsActive: Boolean,
  subscriptionDetails: Object,
  chatCount: Number,
  contactCount: Number,
  campaignCount: Number,
  templateCount: Number,
  setupWhatsapp: Boolean,
  organization: Object,
  campaigns: Object,
  period: Object,
  inbound: Object,
  outbound: Object,
  embeddedSignupActive: Number,
  appId: String,
  configId: String,
  graphAPIVersion: String,
});

const chartOptions = {
  chart: {
    height: 350,
    type: "area",
    toolbar: {
      show: false,
    },
    zoom: {
      enabled: false,
    },
    background: 'transparent',
  },
  colors: ["#6366f1", "#ec4899"],
  fill: {
    type: "gradient",
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.5,
      opacityTo: 0.1,
      stops: [0, 90, 100],
    },
  },
  dataLabels: {
    enabled: false,
  },
  stroke: {
    width: 3,
    curve: "smooth",
  },
  xaxis: {
    type: "datetime",
    categories: props.period,
    labels: {
      style: {
        colors: '#6366f1',
        fontSize: '12px',
      }
    },
    axisBorder: {
      color: '#e0e7ff',
    },
    axisTicks: {
      color: '#e0e7ff',
    }
  },
  yaxis: {
    labels: {
      style: {
        colors: '#6366f1',
        fontSize: '12px',
      }
    }
  },
  tooltip: {
    x: {
      format: "dd/MM/yy HH:mm",
    },
    theme: 'light',
    style: {
      fontSize: '12px',
    }
  },
  grid: {
    borderColor: '#e0e7ff',
    strokeDashArray: 4,
    opacity: 0.5,
  },
  legend: {
    position: 'top',
    horizontalAlign: 'left',
    fontSize: '14px',
    fontWeight: 600,
    labels: {
      colors: '#4338ca',
    },
    markers: {
      width: 12,
      height: 12,
      radius: 6,
    }
  }
};

const series = [
  {
    name: trans("Inbound chats"),
    data: props.inbound,
  },
  {
    name: trans("Outbound chats"),
    data: props.outbound,
  },
];

const teamNotification = ref(true);

const displayTeamNotification = () => {
  try {
    teamNotification.value = props.organization?.metadata
      ? JSON.parse(props.organization.metadata)?.notification?.team ?? true
      : true;
  } catch (error) {
    teamNotification.value = true;
  }

  return teamNotification.value;
};

const dismissNotification = () => {
  router.delete("/dismiss-notification/team", {});
};

const isHistoryModalOpen = ref(false);
const balanceHistory = ref([]);
const selectedUserId = ref(null);

const openHistoryModal = async (id) => {
  selectedUserId.value = id;
  isHistoryModalOpen.value = true;

  try {
    const response = await axios.get(`/api/users/${id}/balance-history`);
    balanceHistory.value = response.data.balance_history;
  } catch (error) {
    console.error("Failed to fetch balance history:", error);
  }
};

const closeHistoryModal = () => {
  isHistoryModalOpen.value = false;
  balanceHistory.value = [];
};
</script>

<style scoped>
@keyframes pulse {

  0%,
  100% {
    opacity: 1;
  }

  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Custom scrollbar for light theme */
:deep(*::-webkit-scrollbar) {
  width: 8px;
  height: 8px;
}

:deep(*::-webkit-scrollbar-track) {
  background: rgba(99, 102, 241, 0.05);
  border-radius: 10px;
}

:deep(*::-webkit-scrollbar-thumb) {
  background: rgba(99, 102, 241, 0.3);
  border-radius: 10px;
}

:deep(*::-webkit-scrollbar-thumb:hover) {
  background: rgba(99, 102, 241, 0.5);
}
</style>