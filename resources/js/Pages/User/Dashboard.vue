<template>
  <AppLayout>
    <div class="min-h-screen p-4 md:p-8">
      <!-- Header Section with Glassmorphism -->
      <div class="relative overflow-hidden bg-white/70 backdrop-blur-xl rounded-md border border-white/20 mb-8">
        <div class="relative z-10">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="space-y-2">
              <h1 class="text-3xl font-bold">
                {{ $t("Welcome backs") }}, {{ props.auth.user.first_name }}! 👋
              </h1>
              <p class="text-gray-600 text-sm md:text-base">{{ $t("Dashboard") }} Overview</p>
            </div>

            <!-- Wallet Card with Animation -->
            <div class="group relative">
              <div class="relative bg-green-500 rounded-md p-4 shadow-lg">
                <div class="flex items-center gap-3">
                  <button @click="openHistoryModal(props.auth.user.id)"
                    class="bg-white/20 p-2 rounded-md backdrop-blur-sm hover:bg-black/10 transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                  </button>
                  <div>
                    <span class="text-white/90 text-sm font-medium">{{ $t("Wallet Balance") }}</span>
                    <p class="text-xl font-bold text-white">₹{{ props.auth.user.balance }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Important Notice -->
          <div class="mt-6 bg-red-50 border-l-4 border-red-500 rounded-md p-4">
            <div class="flex gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <p class="text-sm text-red-800">
                <strong>Note:</strong> If your wallet balance is less than ₹1 or if your campaign cost exceeds your
                available balance, you will not be able to send messages.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <Link href="/contacts/add"
          class="group relative overflow-hidden bg-white hover:bg-gradient-to-br hover:from-blue-50 hover:to-cyan-50 rounded-md p-4 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
        <div class="flex items-center gap-4">
          <div
            class="bg-gradient-to-br from-blue-500 to-cyan-500 p-3 rounded-md group-hover:scale-110 transition-transform duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
          </div>
          <div>
            <h3 class="font-semibold text-gray-900">{{ $t("Add contact") }}</h3>
            <p class="text-sm text-gray-600">Create new contact</p>
          </div>
        </div>
        </Link>

        <Link href="/campaigns/create"
          class="group relative overflow-hidden bg-white hover:bg-gradient-to-br hover:from-purple-50 hover:to-pink-50 rounded-md p-4 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
        <div class="flex items-center gap-4">
          <div
            class="bg-gradient-to-br from-purple-500 to-pink-500 p-3 rounded-md group-hover:scale-110 transition-transform duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
            </svg>
          </div>
          <div>
            <h3 class="font-semibold text-gray-900">{{ $t("Create campaign") }}</h3>
            <p class="text-sm text-gray-600">Start new campaign</p>
          </div>
        </div>
        </Link>

        <Link href="/templates/create"
          class="group relative overflow-hidden bg-white hover:bg-gradient-to-br hover:from-orange-50 hover:to-amber-50 rounded-md p-4 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
        <div class="flex items-center gap-4">
          <div
            class="bg-gradient-to-br from-orange-500 to-amber-500 p-3 rounded-md group-hover:scale-110 transition-transform duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
          </div>
          <div>
            <h3 class="font-semibold text-gray-900">{{ $t("Create template") }}</h3>
            <p class="text-sm text-gray-600">Design new template</p>
          </div>
        </div>
        </Link>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <!-- Contacts Card -->
        <div
          class="group relative bg-white rounded-md p-4 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden">
          <div
            class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-400/10 to-cyan-400/10 rounded-full blur-2xl">
          </div>
          <div class="relative">
            <div class="flex items-center justify-between mb-4">
              <div class="bg-gradient-to-br from-blue-500 to-cyan-500 p-3 rounded-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
            </div>
            <h3 class="text-gray-600 text-sm font-medium mb-1">{{ $t("Contacts") }}</h3>
            <p class="text-3xl font-bold text-gray-900 mb-3">{{ props.contactCount }}</p>
            <Link href="/contacts"
              class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-700 font-medium group-hover:gap-2 transition-all">
            <span>{{ $t("View contacts") }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            </Link>
          </div>
        </div>

        <!-- Campaigns Card -->
        <div
          class="group relative bg-white rounded-md p-4 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden">
          <div
            class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-purple-400/10 to-pink-400/10 rounded-full blur-2xl">
          </div>
          <div class="relative">
            <div class="flex items-center justify-between mb-4">
              <div class="bg-gradient-to-br from-purple-500 to-pink-500 p-3 rounded-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
              </div>
            </div>
            <h3 class="text-gray-600 text-sm font-medium mb-1">{{ $t("Campaigns") }}</h3>
            <p class="text-3xl font-bold text-gray-900 mb-3">{{ props.campaignCount }}</p>
            <Link href="/campaigns"
              class="flex items-center gap-1 text-sm text-purple-600 hover:text-purple-700 font-medium group-hover:gap-2 transition-all">
            <span>{{ $t("View campaigns") }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            </Link>
          </div>
        </div>

        <!-- Templates Card -->
        <div
          class="group relative bg-white rounded-md p-4 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden">
          <div
            class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-orange-400/10 to-amber-400/10 rounded-full blur-2xl">
          </div>
          <div class="relative">
            <div class="flex items-center justify-between mb-4">
              <div class="bg-gradient-to-br from-orange-500 to-amber-500 p-3 rounded-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
            </div>
            <h3 class="text-gray-600 text-sm font-medium mb-1">{{ $t("Templates") }}</h3>
            <p class="text-3xl font-bold text-gray-900 mb-3">{{ props.templateCount }}</p>
            <Link href="/templates"
              class="flex items-center gap-1 text-sm text-orange-600 hover:text-orange-700 font-medium group-hover:gap-2 transition-all">
            <span>{{ $t("View templates") }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            </Link>
          </div>
        </div>

        <!-- All Chats Card -->
        <div
          class="group relative bg-white rounded-md p-4 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden">
          <div
            class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-green-400/10 to-emerald-400/10 rounded-full blur-2xl">
          </div>
          <div class="relative">
            <div class="flex items-center justify-between mb-4">
              <div class="bg-gradient-to-br from-green-500 to-emerald-500 p-3 rounded-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
              </div>
            </div>
            <h3 class="text-gray-600 text-sm font-medium mb-1">{{ $t("All chats") }}</h3>
            <p class="text-3xl font-bold text-gray-900 mb-3">{{ props.chatCount }}</p>
            <Link href="/chats"
              class="flex items-center gap-1 text-sm text-green-600 hover:text-green-700 font-medium group-hover:gap-2 transition-all">
            <span>{{ $t("View chats") }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            </Link>
          </div>
        </div>
      </div>

      <!-- Two Column Layout -->
      <div class="grid md:grid-cols-2 gap-6">
        <!-- Chart Section -->
        <div class="bg-white max-h-max rounded-md p-4 shadow-lg border border-gray-100">
          <div class="mb-4">
            <h3 class="text-xl font-bold text-gray-900">{{ $t("Chat Analytics") }}</h3>
            <p class="text-sm text-gray-600">Inbound vs Outbound chats</p>
          </div>
          <div id="chart">
            <apexchart type="area" height="350" :options="chartOptions" :series="series"></apexchart>
          </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
          <!-- Subscription Status -->
          <div class="relative overflow-hidden rounded-md shadow-lg border border-gray-100" :class="!subscriptionIsActive
            ? 'bg-gradient-to-br from-red-500 to-pink-600 text-white'
            : 'bg-gradient-to-br from-emerald-500 to-teal-600 text-white'
            ">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>
            <div class="relative p-4">
              <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-2">
                    <div class="bg-white/20 p-2 rounded-md backdrop-blur-sm">
                      <svg v-if="subscriptionIsActive" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </div>
                    <h3 class="text-lg font-bold">
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
                  </div>

                  <p class="text-white/90 text-sm mb-4">
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
                    class="inline-flex items-center gap-2 bg-white text-gray-900 px-4 py-2 rounded-md font-medium hover:bg-white/90 transition-all">
                  {{ $t("Subscribe") }}
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                  </Link>

                  <Link
                    v-if="props.auth.user.teams[0].role === 'owner' && props.subscription?.status === 'active' && !subscriptionIsActive"
                    href="/billing"
                    class="inline-flex items-center gap-2 bg-white text-gray-900 px-4 py-2 rounded-md font-medium hover:bg-white/90 transition-all">
                  {{ $t("Add payment") }}
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <!-- Setup WhatsApp -->
          <div v-if="props.auth.user.teams[0].role === 'owner' && props.setupWhatsapp === true"
            class="bg-white rounded-md p-4 shadow-lg border border-gray-100">
            <div class="flex items-start justify-between mb-4">
              <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                  <div class="bg-gradient-to-br from-green-500 to-emerald-500 p-2 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                  </div>
                  <h3 class="text-lg font-bold text-gray-900">{{ $t("Setup whatsapp") }}</h3>
                </div>
                <p class="text-sm text-gray-600 mb-4">
                  {{ $t("Setup your whatsapp API to start using your instance") }}
                </p>
                <EmbeddedSignupBtn v-if="embeddedSignupActive == 1" :appId="props.appId" :configId="props.configId"
                  :graphAPIVersion="props.graphAPIVersion" />
                <Link v-else href="/settings/whatsapp"
                  class="inline-flex items-center gap-2 bg-gradient-to-r from-green-500 to-emerald-500 text-white px-4 py-2 rounded-md font-medium hover:from-green-600 hover:to-emerald-600 transition-all">
                {{ $t("Setup whatsapp") }}
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                </Link>
              </div>
            </div>
          </div>

          <!-- Setup Team -->
          <div v-if="props.auth.user.teams[0].role === 'owner' && displayTeamNotification() === true"
            class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-md p-4 shadow-lg border border-amber-200">
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-center gap-3">
                <div class="bg-gradient-to-br from-amber-500 to-orange-500 p-2 rounded-md">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-gray-900">{{ $t("Setup team") }}</h3>
                  <p class="text-sm text-gray-600">{{ $t("Add other people to your team") }}</p>
                </div>
              </div>
            </div>
            <div class="flex items-center justify-between gap-3">
              <Link href="team"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white px-4 py-2 rounded-md font-medium hover:from-amber-600 hover:to-orange-600 transition-all">
              {{ $t("Add users") }}
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
              </Link>
              <button @click="dismissNotification()" class="text-sm text-gray-600 hover:text-gray-900 underline">
                {{ $t("Dismiss notification") }}
              </button>
            </div>
          </div>

          <!-- Campaigns List -->
          <div class="bg-white rounded-md p-4 shadow-lg border border-gray-100">
            <div class="mb-4">
              <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $t("Campaigns") }}</h3>
              <p class="text-sm text-gray-600">
                {{ $t("Below are your outgoing or scheduled campaigns") }}
              </p>
            </div>

            <div v-if="props.campaigns.length === 0"
              class="border-2 border-dashed border-gray-300 rounded-md p-8 text-center">
              <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
              </div>
              <p class="text-sm text-gray-600 mb-4">
                {{ $t("You do not have any outgoing or scheduled campaigns") }}
              </p>
              <Link href="/campaigns/create"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-[#ff5100] to-[#ff7640] text-white px-4 py-2 rounded-md font-medium hover:shadow-lg transition-all">
              {{ $t("Create campaign") }}
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              </Link>
            </div>

            <div v-else class="space-y-3">
              <div v-for="(item, index) in props.campaigns" :key="index"
                class="group border-l-4 border-orange-500 bg-gradient-to-r from-orange-50 to-transparent hover:from-orange-100 rounded-r-lg p-4 transition-all duration-300">
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <h4 class="font-semibold text-gray-900 capitalize">{{ item.name }}</h4>
                  </div>
                  <span class="bg-amber-100 text-amber-800 text-xs font-semibold px-3 py-1 rounded-full capitalize">
                    {{ item.status }}
                  </span>
                </div>
              </div>
              <Link href="/campaigns"
                class="flex items-center gap-2 text-sm text-orange-600 hover:text-orange-700 font-medium pt-2 group">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 group-hover:translate-x-1 transition-transform"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
              <span>{{ $t("View more campaigns") }}</span>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Balance History Modal -->
    <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0"
      enter-to-class="opacity-100" leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100" leave-to-class="opacity-0">
      <div v-if="isHistoryModalOpen"
        class="fixed inset-0 flex items-center justify-center bg-black/60 backdrop-blur-sm z-50 p-4">
        <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100" leave-active-class="transition-all duration-200 ease-in"
          leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
          <div v-if="isHistoryModalOpen"
            class="bg-white rounded-md shadow-2xl w-full max-w-5xl h-[85vh] flex flex-col overflow-hidden">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-5 border-b bg-gradient-to-r from-gray-50 to-blue-50">
              <div class="flex items-center gap-3">
                <div class="bg-gradient-to-br from-blue-500 to-cyan-500 p-2 rounded-md">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">
                  {{ $t('Balance History') }}
                </h2>
              </div>
              <button @click="closeHistoryModal"
                class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-md transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Table Section -->
            <div class="flex-1 overflow-y-auto">
              <table class="w-full">
                <thead class="sticky top-0 bg-gradient-to-r from-gray-100 to-gray-50 shadow-sm z-10">
                  <tr>
                    <th class="p-4 text-left font-semibold text-gray-700 text-sm">{{ $t('Date') }}</th>
                    <th class="p-4 text-left font-semibold text-gray-700 text-sm">{{ $t('Amount') }}</th>
                    <th class="p-4 text-left font-semibold text-gray-700 text-sm">{{ $t('Balance After') }}</th>
                    <th class="p-4 text-left font-semibold text-gray-700 text-sm">{{ $t('Type') }}</th>
                    <th class="p-4 text-left font-semibold text-gray-700 text-sm">{{ $t('Note') }}</th>
                  </tr>
                </thead>
                <tbody class="text-sm">
                  <tr v-for="record in balanceHistory" :key="record.id"
                    class="border-b border-gray-100 hover:bg-blue-50/50 transition-colors">
                    <td class="p-4 text-gray-700">{{ new Date(record.created_at).toLocaleString() }}</td>
                    <td class="p-4 font-semibold text-blue-600">₹{{ record.amount }}</td>
                    <td class="p-4 text-gray-700">₹{{ record.balance_after }}</td>
                    <td class="p-4">
                      <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold" :class="record.type === 'credit'
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700'">
                        <svg v-if="record.type === 'credit'" xmlns="http://www.w3.org/2000/svg" class="w-3 h-3"
                          fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                          stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                        {{ record.type }}
                      </span>
                    </td>
                    <td class="p-4 text-gray-600">{{ record.note ?? '-' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Footer -->
            <div class="flex justify-end px-6 py-4 border-t bg-gray-50">
              <button @click="closeHistoryModal"
                class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-md shadow-lg hover:shadow-xl hover:from-blue-700 hover:to-cyan-700 transition-all font-medium">
                {{ $t('Close') }}
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </AppLayout>
</template>

<script setup>
import AppLayout from "./Layout/App.vue";
import { Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import { trans } from "laravel-vue-i18n";
import EmbeddedSignupBtn from "@/Components/EmbeddedSignupBtn.vue";
import axios from 'axios';

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
  },
  colors: ["#3b82f6", "#f97316"],
  fill: {
    type: "gradient",
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.4,
      opacityTo: 0.1,
      stops: [0, 90, 100],
    },
  },
  dataLabels: {
    enabled: false,
  },
  stroke: {
    width: 2,
    curve: "smooth",
  },
  xaxis: {
    type: "datetime",
    categories: props.period,
  },
  tooltip: {
    x: {
      format: "dd/MM/yy HH:mm",
    },
  },
  legend: {
    position: 'top',
    horizontalAlign: 'right',
  },
  grid: {
    borderColor: '#f1f5f9',
  },
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