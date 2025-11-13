<template>
  <AppLayout>
    <div class="h-[calc(100vh-65px)] bg-gradient-to-br from-slate-50 via-orange-50/30 to-slate-50 overflow-hidden">
      <!-- Header -->
      <div class="sticky top-0 z-10 bg-white/95 backdrop-blur-sm border-b border-slate-200 shadow-sm">
        <div class="mx-auto px-6 py-4">
          <div class="flex items-center justify-between">
            <!-- Left: Title and Info -->
            <div class="flex items-center space-x-4">
              <div
                class="w-12 h-12 bg-gradient-to-br from-[#ff5100] to-[#ff6422] rounded-xl flex items-center justify-center shadow-lg">
                <FileText class="w-6 h-6 text-white" />
              </div>
              <div>
                <h2 class="text-2xl font-bold text-slate-800">{{ $t("New Template") }}</h2>
                <p class="flex items-center text-sm text-slate-600 mt-1">
                  <span>{{ $t("Create template for review") }}</span>
                </p>
              </div>
            </div>

            <!-- Right: Actions -->
            <div class="flex items-center space-x-3">
              <Link href="/templates"
                class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-all duration-200 border border-slate-300 flex items-center space-x-2">
              <ArrowLeft class="w-4 h-4" />
              <span>{{ $t("Back") }}</span>
              </Link>

              <button v-if="selectedType === 'template'" @click="submitForm()" type="button"
                class="px-6 py-2.5 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
                :class="isFormValid ? 'bg-gradient-to-r from-[#ff5100] to-[#ff6422] hover:from-[#ff6422] hover:to-[#ff5100] text-white' : 'bg-slate-200 text-slate-400'"
                :disabled="!isFormValid || isLoading">
                <template v-if="!isLoading">
                  <Send class="w-4 h-4" />
                  <span>{{ $t("Submit Template") }}</span>
                </template>
                <template v-else>
                  <Loader2 class="w-5 h-5 animate-spin" />
                  <span>{{ $t("Creating...") }}</span>
                </template>
              </button>

              <button v-if="selectedType === 'carousel'" @click="CardsubmitForm"
                class="px-6 py-2.5 bg-gradient-to-r from-[#ff5100] to-[#ff6422] hover:from-[#ff6422] hover:to-[#ff5100] text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-[1.02] flex items-center space-x-2">
                <Send class="w-4 h-4" />
                <span>{{ $t("Submit Template") }}</span>
              </button>

              <button v-if="selectedType === 'flow'" @click="FlowSubmitForm"
                class="px-6 py-2.5 bg-gradient-to-r from-[#ff5100] to-[#ff6422] hover:from-[#ff6422] hover:to-[#ff5100] text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-[1.02] flex items-center space-x-2">
                <Send class="w-4 h-4" />
                <span>{{ $t("Submit Template") }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="flex h-[calc(100vh-152px)]">
        <!-- Left Panel - Form -->
        <div class="w-full lg:w-1/2 overflow-y-auto">
          <!-- Not Connected State -->
          <div v-if="!settings?.whatsapp" class="p-8 flex items-center justify-center min-h-full">
            <div class="max-w-md w-full">
              <div class="bg-white rounded-3xl shadow-xl p-8 border border-slate-200 relative overflow-hidden">
                <div
                  class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-[#ff5100]/10 to-transparent rounded-full -mr-16 -mt-16">
                </div>

                <div class="relative z-10">
                  <div
                    class="w-20 h-20 bg-gradient-to-br from-[#ff5100] to-[#ff6422] rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <MessageSquare class="w-10 h-10 text-white" />
                  </div>

                  <h3 class="text-2xl font-bold text-center mb-3 text-slate-800">
                    {{ $t('Connect WhatsApp') }}
                  </h3>
                  <p class="text-center text-slate-600 mb-6 leading-relaxed">
                    {{ $t('You need to connect your WhatsApp account first before you can create a template.') }}
                  </p>

                  <Link href="/settings/whatsapp"
                    class="w-full flex items-center justify-center space-x-2 bg-gradient-to-r from-[#ff5100] to-[#ff6422] hover:from-[#ff6422] hover:to-[#ff5100] text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-[1.02]">
                  <Zap class="w-5 h-5" />
                  <span>{{ $t('Connect Whatsapp account') }}</span>
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <!-- Form Content -->
          <div v-else class="p-6 lg:p-8 space-y-6">
            <!-- Template Type Selector -->
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200">
              <div class="flex items-center space-x-2 mb-4">
                <Layers class="w-5 h-5 text-[#ff5100]" />
                <h4 class="font-semibold text-slate-800">{{ $t("Template Type") }}</h4>
              </div>

              <div class="grid grid-cols-3 gap-3">
                <button @click="selectedType = 'template'"
                  class="p-4 rounded-xl border-2 transition-all duration-200 flex flex-col items-center space-y-2"
                  :class="selectedType === 'template' ? 'border-[#ff5100] bg-[#ff5100]/5' : 'border-slate-200 hover:border-slate-300'">
                  <FileText :class="selectedType === 'template' ? 'text-[#ff5100]' : 'text-slate-400'"
                    class="w-6 h-6" />
                  <span class="text-sm font-medium"
                    :class="selectedType === 'template' ? 'text-[#ff5100]' : 'text-slate-600'">Template</span>
                </button>

                <button @click="selectedType = 'carousel'"
                  class="p-4 rounded-xl border-2 transition-all duration-200 flex flex-col items-center space-y-2"
                  :class="selectedType === 'carousel' ? 'border-[#ff5100] bg-[#ff5100]/5' : 'border-slate-200 hover:border-slate-300'">
                  <Layers :class="selectedType === 'carousel' ? 'text-[#ff5100]' : 'text-slate-400'" class="w-6 h-6" />
                  <span class="text-sm font-medium"
                    :class="selectedType === 'carousel' ? 'text-[#ff5100]' : 'text-slate-600'">Carousel</span>
                </button>

                <button @click="selectedType = 'flow'"
                  class="p-4 rounded-xl border-2 transition-all duration-200 flex flex-col items-center space-y-2"
                  :class="selectedType === 'flow' ? 'border-[#ff5100] bg-[#ff5100]/5' : 'border-slate-200 hover:border-slate-300'">
                  <Workflow :class="selectedType === 'flow' ? 'text-[#ff5100]' : 'text-slate-400'" class="w-6 h-6" />
                  <span class="text-sm font-medium"
                    :class="selectedType === 'flow' ? 'text-[#ff5100]' : 'text-slate-600'">Flow</span>
                </button>
              </div>
            </div>
            <!-- Template Form -->
            <div v-if="selectedType === 'template'" class="space-y-4">
              <!-- Basic Details Card -->
              <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 space-y-5">
                <div class="flex items-center space-x-2 mb-4">
                  <Settings class="w-5 h-5 text-[#ff5100]" />
                  <h4 class="font-semibold text-slate-800">Basic Details</h4>
                </div>

                <FormInput v-model="form.name" :name="$t('Name')" :type="'text'" @input="handleNameInput"
                  @keydown.space.prevent="addUnderscore" :class="'w-full'" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <FormSelect v-model="form.category" :options="categoryOptions" :name="$t('Category')"
                    :placeholder="$t('Select category')" />
                  <FormSelect v-model="form.language" :options="langOptions" :name="$t('Language')"
                    :placeholder="$t('Select language')" />
                </div>
              </div>

              <!-- Body Section (for UTILITY and MARKETING) -->
              <div v-if="form.category === 'UTILITY' || form.category === 'MARKETING'"
                class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 space-y-4">
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-2">
                    <FileText class="w-5 h-5 text-[#ff5100]" />
                    <h4 class="font-semibold text-slate-800">{{ $t("Body") }}</h4>
                    <span
                      class="px-2 py-1 bg-[#ff5100]/10 text-[#ff5100] text-xs font-medium rounded-full">Required</span>
                  </div>
                </div>
                <p class="text-sm text-slate-600">
                  {{ $t("Enter the text for your message in the language that you've selected") }}
                </p>
                <BodyTextArea v-model="form.body.text" @updateExamples="updateBodyExamples" />
              </div>

              <!-- Header Section -->
              <div v-if="form.category === 'UTILITY' || form.category === 'MARKETING'"
                class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 space-y-4">
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-2">
                    <ImageIcon class="w-5 h-5 text-[#ff5100]" />
                    <h4 class="font-semibold text-slate-800">{{ $t("Header") }}</h4>
                    <span class="px-2 py-1 bg-slate-200 text-slate-600 text-xs font-medium rounded-full">Optional</span>
                  </div>
                </div>
                <p class="text-sm text-slate-600">
                  {{ $t("Add a title or choose which type of media you'll use for this header") }}
                </p>

                <!-- Header Type Selector -->
                <div class="grid grid-cols-4 gap-2 p-2 bg-slate-50 rounded-xl">
                  <button @click="changeHeaderType('TEXT')"
                    class="py-3 px-4 rounded-lg text-sm font-medium transition-all duration-200"
                    :class="form.header.format === 'TEXT' ? 'bg-white shadow-md text-[#ff5100]' : 'text-slate-600 hover:bg-white/50'">
                    {{ $t("Text") }}
                  </button>
                  <button @click="changeHeaderType('IMAGE')"
                    class="py-3 px-4 rounded-lg text-sm font-medium transition-all duration-200"
                    :class="form.header.format === 'IMAGE' ? 'bg-white shadow-md text-[#ff5100]' : 'text-slate-600 hover:bg-white/50'">
                    {{ $t("Image") }}
                  </button>
                  <button @click="changeHeaderType('VIDEO')"
                    class="py-3 px-4 rounded-lg text-sm font-medium transition-all duration-200"
                    :class="form.header.format === 'VIDEO' ? 'bg-white shadow-md text-[#ff5100]' : 'text-slate-600 hover:bg-white/50'">
                    {{ $t("Video") }}
                  </button>
                  <button @click="changeHeaderType('DOCUMENT')"
                    class="py-3 px-4 rounded-lg text-sm font-medium transition-all duration-200"
                    :class="form.header.format === 'DOCUMENT' ? 'bg-white shadow-md text-[#ff5100]' : 'text-slate-600 hover:bg-white/50'">
                    {{ $t("Document") }}
                  </button>
                </div>

                <!-- Header Content -->
                <div v-if="form.header.format === 'TEXT'">
                  <HeaderTextArea v-model="form.header.text" :customValues="form.header.example"
                    @updateExamples="updateHeaderExamples" />
                </div>

                <!-- File Upload for IMAGE/VIDEO/DOCUMENT -->
                <div v-else
                  class="border-2 border-dashed border-slate-300 rounded-xl p-8 hover:border-[#ff5100] transition-colors">
                  <input type="file" class="hidden"
                    :accept="form.header.format === 'IMAGE' ? '.jpg, .png' : form.header.format === 'VIDEO' ? '.mp4' : '.pdf'"
                    :id="`file-upload-${form.header.format}`" @change="handleFileUpload($event)" />

                  <div v-if="form.header.example"
                    class="flex items-center justify-center space-x-3 p-3 bg-slate-50 rounded-lg">
                    <component
                      :is="form.header.format === 'IMAGE' ? ImageIcon : form.header.format === 'VIDEO' ? Video : FileIcon"
                      class="w-6 h-6 text-[#ff5100]" />
                    <span class="text-sm font-medium text-slate-700">{{ form.header.example.name }}</span>
                    <button @click="removeFile()" class="p-1 hover:bg-slate-200 rounded-full transition-colors">
                      <X class="w-4 h-4 text-slate-600" />
                    </button>
                  </div>

                  <label v-else :for="`file-upload-${form.header.format}`"
                    class="cursor-pointer flex flex-col items-center space-y-3">
                    <component
                      :is="form.header.format === 'IMAGE' ? ImageIcon : form.header.format === 'VIDEO' ? Video : FileIcon"
                      class="w-12 h-12 text-slate-400" />
                    <span class="text-sm font-medium text-slate-600">
                      {{ $t("Provide examples of the variables or media in the header") }}
                    </span>
                    <span class="text-xs text-slate-500">
                      {{ form.header.format === 'IMAGE' ? 'PNG or JPG files only' :
                        form.header.format === 'VIDEO' ? 'MP4 files only' : 'PDF files only' }}
                    </span>
                  </label>
                </div>
              </div>

              <!-- Footer Section -->
              <div v-if="form.category === 'UTILITY' || form.category === 'MARKETING'"
                class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 space-y-4">
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-2">
                    <AlignLeft class="w-5 h-5 text-[#ff5100]" />
                    <h4 class="font-semibold text-slate-800">{{ $t("Footer") }}</h4>
                    <span class="px-2 py-1 bg-slate-200 text-slate-600 text-xs font-medium rounded-full">Optional</span>
                  </div>
                </div>
                <p class="text-sm text-slate-600">
                  {{ $t("Add a short line of text to the bottom of your message template") }}
                </p>
                <FormTextArea v-model="form.footer.text" @input="characterCount('footer')" :name="$t('Footer text')"
                  :showLabel="false" :textAreaRows="2" />
                <span class="text-xs text-slate-500">{{ $t("Characters") }}: {{ footerCharacterCount }}/{{
                  footerCharacterLimit }}</span>
              </div>

              <!-- Buttons Section -->
              <div v-if="form.category === 'UTILITY' || form.category === 'MARKETING'"
                class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 space-y-4">
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-2">
                    <Zap class="w-5 h-5 text-[#ff5100]" />
                    <h4 class="font-semibold text-slate-800">{{ $t("Buttons") }}</h4>
                    <span class="px-2 py-1 bg-slate-200 text-slate-600 text-xs font-medium rounded-full">Optional</span>
                  </div>
                </div>
                <p class="text-sm text-slate-600">
                  {{ $t("Create buttons that let customers respond to your message or take action") }}
                </p>

                <!-- Button Add Options -->
                <div class="grid grid-cols-2 gap-3">
                  <button @click="addButton('call')"
                    class="flex items-center justify-center space-x-2 p-3 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition-all duration-200 text-sm font-medium text-slate-700">
                    <Phone class="w-4 h-4" />
                    <span>{{ $t("Call phone number (1)") }}</span>
                  </button>
                  <button @click="addButton('website')"
                    class="flex items-center justify-center space-x-2 p-3 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition-all duration-200 text-sm font-medium text-slate-700">
                    <Link2 class="w-4 h-4" />
                    <span>{{ $t("Visit website (2)") }}</span>
                  </button>
                  <button @click="addButton('offer')"
                    class="flex items-center justify-center space-x-2 p-3 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition-all duration-200 text-sm font-medium text-slate-700">
                    <Copy class="w-4 h-4" />
                    <span>{{ $t("Copy offer code (1)") }}</span>
                  </button>
                  <button @click="addButton('custom')"
                    class="flex items-center justify-center space-x-2 p-3 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition-all duration-200 text-sm font-medium text-slate-700">
                    <MousePointerClick class="w-4 h-4" />
                    <span>{{ $t("Custom button (6)") }}</span>
                  </button>
                </div>

                <!-- Added Buttons List -->
                <div v-if="form.buttons.length > 0" class="space-y-3">
                  <div v-for="(button, index) in form.buttons" :key="index"
                    class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                    <div class="flex items-center justify-between mb-3">
                      <span class="text-sm font-medium text-slate-700">{{ $t(formatText(button.type)) }}</span>
                      <button @click="removeButton(index)"
                        class="p-1.5 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition-colors">
                        <Trash2 class="w-4 h-4" />
                      </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                      <FormInput v-model="button.text" :name="$t('Button text')" :type="'text'" :labelClass="'mb-0'" />
                      <FormInput v-if="button.type === 'URL'" v-model="button.url" :name="$t('Website url')"
                        :type="'url'" :labelClass="'mb-0'" />
                      <FormInput v-if="button.type === 'PHONE_NUMBER'" v-model="button.country" :name="$t('Country')"
                        :type="'text'" :labelClass="'mb-0'" />
                      <FormInput v-if="button.type === 'PHONE_NUMBER'" v-model="button.phone_number"
                        :name="$t('Phone number')" :type="'text'" :labelClass="'mb-0'" />
                      <FormInput v-if="button.type === 'copy_code'" v-model="button.example" :name="$t('Sample code')"
                        :type="'text'" :labelClass="'mb-0'" />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Authentication Category Sections -->
              <div v-if="form.category === 'AUTHENTICATION'" class="space-y-6">
                <!-- Code Delivery Setup -->
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 space-y-4">
                  <div class="flex items-center space-x-2">
                    <Shield class="w-5 h-5 text-[#ff5100]" />
                    <h4 class="font-semibold text-slate-800">{{ $t("Code delivery setup") }}</h4>
                  </div>
                  <p class="text-sm text-slate-600">
                    {{ $t("Choose how customers send the code from WhatsApp to your app.") }}
                  </p>

                  <div class="space-y-3">
                    <div v-for="option in codeDeliveryOptions" :key="option.value"
                      class="p-4 border-2 rounded-xl transition-all duration-200 cursor-pointer"
                      :class="form.authentication_button.otp_type === option.value ? 'border-[#ff5100] bg-[#ff5100]/5' : 'border-slate-200 hover:border-slate-300'"
                      @click="form.authentication_button.otp_type = option.value">
                      <div class="flex items-start space-x-3">
                        <div class="mt-0.5">
                          <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                            :class="form.authentication_button.otp_type === option.value ? 'border-[#ff5100]' : 'border-slate-400'">
                            <div v-if="form.authentication_button.otp_type === option.value"
                              class="w-2.5 h-2.5 rounded-full bg-[#ff5100]"></div>
                          </div>
                        </div>
                        <div class="flex-1">
                          <label class="font-medium text-slate-800 cursor-pointer">{{ $t(option.label) }}</label>
                          <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ $t(option.description) }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- App Setup (for one_tap and zero_tap) -->
                <div v-if="form.authentication_button.otp_type !== 'copy_code'"
                  class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 space-y-4">
                  <div class="flex items-center space-x-2">
                    <Smartphone class="w-5 h-5 text-[#ff5100]" />
                    <h4 class="font-semibold text-slate-800">{{ $t("App setup") }}</h4>
                  </div>
                  <p class="text-sm text-slate-600">{{ $t("You can add up to 5 apps.") }}</p>

                  <div v-for="(item, index) in form.authentication_button.supported_apps" :key="index"
                    class="flex gap-3 p-4 bg-slate-50 rounded-xl">
                    <FormInput v-model="form.authentication_button.supported_apps[index].package_name"
                      :name="$t('Package name')" :type="'text'" :class="'flex-1'" :labelClass="'mb-0'" />
                    <FormInput v-model="form.authentication_button.supported_apps[index].signature_hash"
                      :name="$t('App signature hash')" :type="'text'" :class="'flex-1'" :labelClass="'mb-0'" />
                    <button v-if="form.authentication_button.supported_apps.length > 1"
                      @click="removeSupportedApp(index)"
                      class="mt-7 p-2 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition-colors">
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>

                  <button v-if="form.authentication_button.supported_apps.length < 5" @click="addSupportedApp()"
                    class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-all duration-200 border border-slate-300 flex items-center justify-center space-x-2">
                    <Plus class="w-4 h-4" />
                    <span>{{ $t("Add another app") }}</span>
                  </button>
                </div>

                <!-- Content Options -->
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 space-y-4">
                  <div class="flex items-center space-x-2">
                    <FileText class="w-5 h-5 text-[#ff5100]" />
                    <h4 class="font-semibold text-slate-800">{{ $t("Content") }}</h4>
                  </div>
                  <p class="text-sm text-slate-600">
                    {{ $t(`Content for authentication message templates can't be edited. You can add additional content
                    from
                    the options below.`) }}
                  </p>

                  <div class="space-y-3">
                    <label
                      class="flex items-center space-x-3 p-3 bg-slate-50 rounded-lg cursor-pointer hover:bg-slate-100 transition-colors">
                      <input v-model="form.body.add_security_recommendation" type="checkbox"
                        class="w-5 h-5 rounded border-slate-300 text-[#ff5100] focus:ring-[#ff5100] cursor-pointer" />
                      <span class="text-sm font-medium text-slate-700">{{ $t("Add security recommendation") }}</span>
                    </label>

                    <label
                      class="flex items-start space-x-3 p-3 bg-slate-50 rounded-lg cursor-pointer hover:bg-slate-100 transition-colors">
                      <input v-model="form.code_expiration" type="checkbox"
                        class="w-5 h-5 rounded border-slate-300 text-[#ff5100] focus:ring-[#ff5100] cursor-pointer mt-0.5" />
                      <div>
                        <div class="text-sm font-medium text-slate-700">{{ $t("Add expiry time for the code") }}</div>
                        <p class="text-xs text-slate-600 mt-1">
                          {{ $t("After the code has expired, the auto-fill button will be disabled.") }}
                        </p>
                      </div>
                    </label>

                    <div v-if="form.code_expiration" class="p-4 bg-white border border-slate-200 rounded-xl">
                      <label class="block text-sm font-medium text-slate-700 mb-2">{{ $t("Expires In") }}</label>
                      <div class="flex items-center space-x-2">
                        <input type="number" v-model="form.footer.code_expiration_minutes" step="any"
                          class="w-32 px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5100] focus:border-transparent" />
                        <span class="text-sm text-slate-600">{{ $t("Minutes") }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Authentication Buttons -->
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 space-y-4">
                  <div class="flex items-center space-x-2">
                    <MousePointerClick class="w-5 h-5 text-[#ff5100]" />
                    <h4 class="font-semibold text-slate-800">{{ $t("Buttons") }}</h4>
                  </div>
                  <p class="text-sm text-slate-600">
                    {{ $t("You can customise the button text for both auto-fill and copy code.") }}
                  </p>

                  <div v-if="form.authentication_button.otp_type === 'copy_code'">
                    <FormInput v-model="form.authentication_button.text" :name="$t('Copy code')" :type="'text'" />
                  </div>
                  <div v-else class="grid grid-cols-2 gap-4">
                    <FormInput v-model="form.authentication_button.autofill_text" :name="$t('Auto-fill')"
                      :type="'text'" />
                    <FormInput v-model="form.authentication_button.text" :name="$t('Copy code')" :type="'text'" />
                  </div>
                </div>
              </div>

              <!-- Message Validity Period -->
              <div v-if="form.category === 'UTILITY' || form.category === 'AUTHENTICATION'"
                class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 space-y-4">
                <div class="flex items-center space-x-2">
                  <Clock class="w-5 h-5 text-[#ff5100]" />
                  <h4 class="font-semibold text-slate-800">{{ $t("Message validity period") }}</h4>
                </div>
                <p class="text-sm text-slate-600">
                  {{ $t("Set a custom validity period for your message delivery.") }}
                </p>

                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                  <div class="flex-1">
                    <label class="text-sm font-medium text-slate-700">
                      {{ $t("Set custom validity period") }}
                    </label>
                    <p class="text-xs text-slate-600 mt-1">
                      {{ $t("Default is 10 minutes if not set") }}
                    </p>
                  </div>
                  <FormToggleSwitch v-model="form.customize_ttl" />
                </div>

                <FormSelect v-if="form.customize_ttl" v-model="form.message_send_ttl_seconds"
                  :options="form.category === 'UTILITY' ? utilityTTLOptions : authTTLOptions"
                  :name="$t('Validity period')" :placeholder="$t('Select validity period')" />
              </div>
            </div>
            <!-- Carousel Form -->
            <div v-if="selectedType === 'carousel'" class="space-y-4">
              <!-- Basic Details -->
              <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 space-y-5">
                <div class="flex items-center space-x-2 mb-4">
                  <Settings class="w-5 h-5 text-[#ff5100]" />
                  <h4 class="font-semibold text-slate-800">Basic Details</h4>
                </div>

                <FormInput v-model="cardform.name" :name="$t('Name')" type="text" @input="handleNameInput"
                  @keydown.space.prevent="addUnderscore" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <FormSelect v-model="cardform.category" :options="categoryCrouselOptions" :name="$t('Category')"
                    :placeholder="$t('Select category')" />
                  <FormSelect v-model="cardform.language" :options="langOptions" :name="$t('Language')"
                    :placeholder="$t('Select language')" />
                </div>
              </div>

              <!-- Body Text -->
              <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 space-y-4">
                <div class="flex items-center space-x-2">
                  <FileText class="w-5 h-5 text-[#ff5100]" />
                  <h4 class="font-semibold text-slate-800">{{ $t("Body") }}</h4>
                  <span
                    class="px-2 py-1 bg-[#ff5100]/10 text-[#ff5100] text-xs font-medium rounded-full">Required</span>
                </div>
                <BodyTextArea v-model="cardform.body.text" @updateExamples="updateBodyExamples" />
              </div>

              <!-- Carousel Cards -->
              <div class="space-y-4">
                <div v-for="(card, cardIndex) in cards" :key="cardIndex"
                  class="bg-white rounded-2xl shadow-sm p-6 border-2 border-slate-200">
                  <!-- Card Header -->
                  <div class="flex items-center justify-between mb-4 pb-4 border-b border-slate-200">
                    <div class="flex items-center space-x-3">
                      <div
                        class="w-10 h-10 bg-gradient-to-br from-[#ff5100] to-[#ff6422] rounded-xl flex items-center justify-center text-white font-bold">
                        {{ cardIndex + 1 }}
                      </div>
                      <h3 class="text-lg font-semibold text-slate-800">Card {{ cardIndex + 1 }}</h3>
                    </div>
                    <button @click="removeCard(cardIndex)"
                      class="px-3 py-2 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition-colors flex items-center space-x-2">
                      <Trash2 class="w-4 h-4" />
                      <span class="text-sm font-medium">Remove</span>
                    </button>
                  </div>

                  <!-- Image Upload -->
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Header Image</label>
                    <div class="flex items-center space-x-3">
                      <label
                        class="cursor-pointer flex items-center justify-center space-x-2 px-4 py-2 bg-gradient-to-r from-[#ff5100] to-[#ff6422] text-white rounded-lg hover:shadow-lg transition-all">
                        <Upload class="w-4 h-4" />
                        <span>Upload Image</span>
                        <input type="file" accept="image/*" @change="(e) => onFileChange(e, cardIndex)"
                          class="hidden" />
                      </label>
                      <span v-if="uploadingIndex === cardIndex"
                        class="text-sm text-[#ff5100] flex items-center space-x-2">
                        <Loader2 class="w-4 h-4 animate-spin" />
                        <span>Uploading...</span>
                      </span>
                      <span v-else-if="cards[cardIndex].components[0].example.header_handle[0]"
                        class="text-sm text-green-600 flex items-center space-x-2">
                        <CheckCircle class="w-4 h-4" />
                        <span>Uploaded</span>
                      </span>
                    </div>
                  </div>

                  <!-- Body Text -->
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Body Text</label>
                    <textarea v-model="card.components[1].text"
                      class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5100] focus:border-transparent"
                      rows="3" placeholder="Enter body text"></textarea>
                  </div>

                  <!-- Buttons -->
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Buttons</label>
                    <div class="space-y-2">
                      <div v-for="(btn, btnIndex) in card.components[2].buttons" :key="btnIndex"
                        class="flex items-center gap-2 p-3 bg-slate-50 rounded-lg flex-wrap">
                        <select v-model="btn.type"
                          class="px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5100]">
                          <option value="quick_reply">Quick Reply</option>
                          <option value="url">URL</option>
                        </select>
                        <input v-model="btn.text" placeholder="Button Text"
                          class="flex-1 px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5100]" />
                        <input v-if="btn.type === 'url'" v-model="btn.url" placeholder="URL"
                          class="flex-1 px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5100]" />
                        <input v-if="btn.type === 'url'" v-model="btn.example[0]" placeholder="Example"
                          class="flex-1 px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5100]" />
                        <button @click="removeCardButton(cardIndex, btnIndex)"
                          class="p-2 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition-colors">
                          <Trash2 class="w-4 h-4" />
                        </button>
                      </div>
                    </div>

                    <button v-if="card.components[2].buttons.length < 3" @click="addCardButton(cardIndex)"
                      class="mt-3 w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition-colors flex items-center justify-center space-x-2">
                      <Plus class="w-4 h-4" />
                      <span>Add Button</span>
                    </button>
                  </div>
                </div>

                <!-- Add Card Button -->
                <button v-if="cards.length < 10" @click="addCard"
                  class="w-full py-3 bg-gradient-to-r from-[#ff5100] to-[#ff6422] hover:from-[#ff6422] hover:to-[#ff5100] text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                  <Plus class="w-5 h-5" />
                  <span>Add Card</span>
                </button>
              </div>
            </div>

            <!-- Flow Form -->
            <div v-if="selectedType === 'flow'" class="space-y-4">
              <!-- Basic Details -->
              <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 space-y-5">
                <div class="flex items-center space-x-2 mb-4">
                  <Settings class="w-5 h-5 text-[#ff5100]" />
                  <h4 class="font-semibold text-slate-800">Basic Details</h4>
                </div>

                <FormInput v-model="flowform.name" :name="$t('Name')" type="text" @input="handleNameInput"
                  @keydown.space.prevent="addUnderscore" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <FormSelect v-model="flowform.category" :options="categoryCrouselOptions" :name="$t('Category')"
                    :placeholder="$t('Select category')" />
                  <FormSelect v-model="flowform.language" :options="langOptions" :name="$t('Language')"
                    :placeholder="$t('Select language')" />
                </div>
              </div>

              <!-- Body Text -->
              <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 space-y-4">
                <div class="flex items-center space-x-2">
                  <FileText class="w-5 h-5 text-[#ff5100]" />
                  <h4 class="font-semibold text-slate-800">{{ $t("Body") }}</h4>
                  <span
                    class="px-2 py-1 bg-[#ff5100]/10 text-[#ff5100] text-xs font-medium rounded-full">Required</span>
                </div>
                <BodyTextArea v-model="flowform.body.text" @updateExamples="updateBodyExamples" />
              </div>

              <!-- Flow Configuration -->
              <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 space-y-4">
                <div class="flex items-center space-x-2">
                  <Workflow class="w-5 h-5 text-[#ff5100]" />
                  <h4 class="font-semibold text-slate-800">Flow Configuration</h4>
                </div>

                <FormInput v-model="flowform.buttonText" :name="$t('Button Text')" type="text" />

                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">{{ $t("Flow JSON") }}</label>
                  <textarea v-model="flowform.flowJson" rows="6"
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff5100] focus:border-transparent font-mono text-sm"
                    placeholder="Paste the JSON here"></textarea>
                </div>

                <button @click="showFlowBuilder = true"
                  class="w-full py-3 bg-gradient-to-r from-[#ff5100] to-[#ff6422] hover:from-[#ff6422] hover:to-[#ff5100] text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                  <ExternalLink class="w-5 h-5" />
                  <span>{{ $t("Create Form") }}</span>
                </button>

                <p class="text-sm text-slate-600 flex items-start space-x-2">
                  <Info class="w-4 h-4 mt-0.5 flex-shrink-0 text-[#ff5100]" />
                  <span>{{ $t("Note: Use the popup to create your flow. Copy the JSON from there and paste it above.")
                    }}</span>
                </p>
              </div>
            </div>
          </div>
        </div>
        <!-- Right Panel - Preview -->
        <div
          class="w-full lg:w-1/2 bg-gradient-to-br from-slate-100 via-orange-50/20 to-slate-100 p-4 flex items-center justify-center relative">
          <!-- Background Decorations -->
          <div
            class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-[#ff5100]/5 to-transparent rounded-full blur-3xl">
          </div>
          <div
            class="absolute bottom-0 left-0 w-80 h-80 bg-gradient-to-tr from-orange-200/20 to-transparent rounded-full blur-3xl">
          </div>

          <!-- Preview Container -->
          <div class="lg:sticky top-0 lg:z-10">
            <div class="text-center my-6">
              <div
                class="inline-flex items-center space-x-2 px-4 py-2 bg-white/80 backdrop-blur-sm rounded-full shadow-sm border border-slate-200 mb-3">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-sm font-medium text-slate-700">Live Preview</span>
              </div>
              <h3 class="text-xl font-bold text-slate-800 mb-1">WhatsApp Message Preview</h3>
              <p class="text-sm text-slate-600">See how your message will appear</p>
            </div>

            <!-- Phone Frame -->
            <div class="bg-white rounded-3xl shadow-2xl p-0 border-8 border-slate-800 relative">
              <!-- Phone Notch -->
              <div class="absolute z-50 top-0 left-1/2 -translate-x-1/2 w-24 h-3 bg-slate-800 rounded-b-2xl">
              </div>

              <!-- WhatsApp Preview -->
              <div class="w-full chat-bg pt-4 rounded-2xl overflow-hidden h-[calc(100vh-380px)] aspect-[12/16]">
                <div class="overflow-y-auto h-full p-4">
                  <!-- Template Preview -->
                  <div v-if="selectedType === 'template'" class="space-y-2">
                    <div
                      class="mr-auto rounded-lg rounded-tl-none my-[0.1em] p-2 text-sm bg-white flex flex-col relative speech-bubble-left max-w-[60%] !text-wrap overflow-x-auto">
                      <!-- Header -->
                      <div v-if="form.header.format !== 'TEXT'"
                        class="mb-3 bg-slate-200 w-full aspect-video flex items-center justify-center rounded-lg">
                        <img v-if="form.header.format === 'IMAGE' && !!form.header.file_url" :src="form.header.file_url"
                          alt="Image" class="w-full object-cover" />

                        <video v-else-if="form.header.format === 'VIDEO' && !!form.header.file_url"
                          :src="form.header.file_url" autoplay muted loop playsinline
                          class="w-full aspect-video object-cover rounded-xl"></video>

                        <component v-else
                          :is="form.header.format === 'IMAGE' ? ImageIcon : form.header.format === 'VIDEO' ? Video : FileIcon"
                          class="w-12 h-12 text-slate-400" />

                      </div>
                      <h2 v-else-if="form.header.text" class="text-slate-700 text-sm mb-2 font-medium">
                        {{ form.header.text }}
                      </h2>

                      <!-- Body -->
                      <p v-if="form.category !== 'AUTHENTICATION'" class="text-sm text-slate-800 whitespace-pre-wrap"
                        v-html="formattedMessage || 'Your message preview will appear here...'"></p>
                      <p v-else class="text-sm text-slate-800 whitespace-pre-wrap" v-html="form.body.add_security_recommendation
                        ? '{{1}} is your verification code. For your security, do not share this code.'
                        : '{{1}} is your verification code.'"></p>

                      <!-- Footer -->
                      <div class="text-slate-500 mt-2 text-xs">
                        <span v-if="form.category !== 'AUTHENTICATION'">{{ form.footer.text }}</span>
                        <span v-else-if="form.code_expiration">
                          {{ $t("This code expires in") }} {{ form.footer.code_expiration_minutes }} minutes
                        </span>
                        <span class="float-right">9:15</span>
                      </div>
                    </div>

                    <!-- Buttons -->
                    <div v-if="form.buttons.length > 0" class="space-y-1 max-w-[60%]">
                      <button v-for="(item, index) in form.buttons" :key="index"
                        class="w-full flex items-center justify-center space-x-2 bg-white text-blue-600 py-2 rounded-lg text-sm font-medium">
                        <component
                          :is="item.type === 'copy_code' ? Copy : item.type === 'PHONE_NUMBER' ? Phone : item.type === 'URL' ? SquareArrowOutUpRight : Reply"
                          class="w-4 h-4" />
                        <span>{{ item.text }}</span>
                      </button>
                    </div>

                    <!-- Auth Buttons -->
                    <div v-if="form.category === 'AUTHENTICATION' && form.authentication_button.otp_type !== 'zero_tap'"
                      class="max-w-[60%]">
                      <button
                        class="w-full flex items-center justify-center space-x-2 bg-white text-blue-600 py-2 rounded-lg text-sm font-medium">
                        <Copy class="w-4 h-4" />
                        <span v-if="form.authentication_button.otp_type === 'copy_code'">
                          {{ form.authentication_button.text }}
                        </span>
                        <span v-else>{{ form.authentication_button.autofill_text }}</span>
                      </button>
                    </div>
                  </div>

                  <!-- Carousel Preview -->
                  <div v-if="selectedType === 'carousel'" class="space-y-2">
                    <div
                      class="mr-auto rounded-lg rounded-tl-none my-[0.1em] p-2 text-sm bg-white flex flex-col relative speech-bubble-left max-w-[60%] !text-wrap overflow-x-auto">
                      <p class="text-sm text-slate-800 whitespace-pre-wrap">
                        {{ cardform.body.text || 'Your message preview will appear here...' }}
                      </p>
                    </div>

                    <div class="flex space-x-2 overflow-x-auto pb-2">
                      <div v-for="(card, index) in cards" :key="index"
                        class="w-[150px] bg-white rounded-lg shadow-md flex flex-col justify-between p-3 flex-shrink-0">
                        <div>
                          <div
                            class="mb-3 w-[126px] aspect-video bg-slate-200 flex items-center justify-center rounded-lg">
                            <img v-if="card.image_url" :src="card.image_url" alt="Image"
                              class="w-full aspect-video object-cover" />
                            <component v-else :is="ImageIcon" class="w-12 h-12 text-slate-400" />
                          </div>
                          <div v-if="card.components.find(c => c.type === 'body')" class="mb-2">
                            <p class="text-sm text-slate-700">
                              {{card.components.find(c => c.type === 'body')?.text || 'No text'}}
                            </p>
                          </div>
                        </div>
                        <div v-if="card.components.find(c => c.type === 'buttons')" class="space-y-1">
                          <button v-for="(btn, btnIndex) in card.components.find(c => c.type === 'buttons')?.buttons"
                            :key="btnIndex"
                            class="w-full flex items-center justify-center space-x-2 text-xs bg-blue-500/5 text-blue-600 py-1.5 rounded text-center">
                            <Reply v-if="btn.type === 'quick_reply'" class="w-4 h-4" />
                            <SquareArrowOutUpRight v-else-if="btn.type === 'url'" class="w-4 h-4" />
                            <span>{{ btn.text || 'Button text' }}</span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Flow Preview -->
                  <div v-if="selectedType === 'flow'" class="space-y-2">
                    <div
                      class="mr-auto rounded-lg rounded-tl-none my-[0.1em] p-2 text-sm bg-white flex flex-col relative speech-bubble-left max-w-[60%] !text-wrap overflow-x-auto">
                      <p class="text-sm text-slate-800 whitespace-pre-wrap">
                        {{ flowform.body.text || 'Your message preview will appear here...' }}
                      </p>
                    </div>
                    <button
                      class="w-full max-w-[60%] flex items-center justify-center space-x-2 bg-white text-blue-600 py-2 rounded-lg text-sm font-medium">
                      <span>{{ flowform.buttonText || 'Button text' }}</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <!-- Preview Info -->
            <div class="my-4 text-center">
              <p class="text-xs text-slate-500">
                Preview may differ slightly from actual WhatsApp appearance
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Flow Builder Modal -->
    <div v-if="showFlowBuilder"
      class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl relative animate-in zoom-in duration-300">
        <button @click="showFlowBuilder = false"
          class="absolute top-4 right-4 p-2 hover:bg-slate-100 rounded-lg transition-colors">
          <X class="w-5 h-5 text-slate-600" />
        </button>

        <div class="mb-6">
          <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center mb-4">
            <Workflow class="w-6 h-6 text-white" />
          </div>
          <h2 class="text-2xl font-bold text-slate-800 mb-2">Build WhatsApp Flow</h2>
          <p class="text-sm text-slate-600">
            Open the official <strong>Facebook Flow Playground</strong> to build your form. Copy the JSON output and
            paste
            it in the form.
          </p>
        </div>

        <!-- open flow playground in new tab -->
        <!-- <a href="https://developers.facebook.com/docs/whatsapp/flows/playground" target="_blank" rel="noopener"
          class="w-full flex items-center justify-center space-x-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl">
          <ExternalLink class="w-5 h-5" />
          <span>Open Flow Playground</span>
        </a> -->

        <!-- open flow playground in same tab -->
        <a href="#" onclick="window.open(
              `https://developers.facebook.com/docs/whatsapp/flows/playground`,
              `FlowPlayground`,
              `width=1200,height=700,left=150,top=100,toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes`
            ); return false;"
          class="w-full flex items-center justify-center space-x-2 bg-gradient-to-r from-[#ff5100] to-[#ff6422] hover:from-[#ff6422] hover:to-[#ff5100] text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7v7m0 0L10 21l-7-7L21 10z" />
          </svg>
          <span>Open Flow Playground</span>
        </a>

        <p class="text-xs text-slate-500 mt-4 flex items-start space-x-2">
          <Info class="w-4 h-4 mt-0.5 flex-shrink-0" />
          <span>Note: Facebook prevents their tool from being embedded. It opens in a new tab.</span>
        </p>
      </div>
    </div>

    <!-- Success/Error Modal -->
    <Modal :label="''" :isOpen="isModalOpen" :showHeader="false">
      <div class="p-6">
        <div v-if="error" class="text-center">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <X class="w-8 h-8 text-red-600" />
          </div>
          <h4 class="text-lg text-slate-800 font-semibold mb-2 text-start">{{ error.error.error_user_title
            }}
          </h4>
          <p class="text-sm text-slate-600 mb-6 text-start">{{ error.error.error_user_msg }}</p>
          <button @click="closeModal"
            class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-colors">
            Close
          </button>
        </div>
        <div v-else class="text-center">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <Loader2 class="w-8 h-8 text-green-600 animate-spin" />
          </div>
          <h3 class="text-xl font-bold text-slate-800 mb-2">{{ $t("Uploading Template") }}</h3>
          <p class="text-sm text-slate-600">{{ $t("Your template is being uploaded!") }}</p>
        </div>
      </div>
    </Modal>

    <!-- Loading Overlay -->
    <div v-if="isLoading && !isModalOpen"
      class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center">
      <div class="bg-white rounded-2xl p-8 shadow-2xl flex flex-col items-center space-y-4">
        <Loader2 class="w-12 h-12 animate-spin text-[#ff5100]" />
        <p class="text-slate-700 font-medium">Processing...</p>
      </div>
    </div>
  </AppLayout>
</template>


<script>
export default {
  data() {
    return {
      showFlowBuilder: false,
      flowform: {
        name: "",
        category: "",
        language: "",
        body: { text: "" },
        buttonText: "",
        flowJson: "",
      },
      selectedType: "template",
      uploadingIndex: null,
    };
  },
  methods: {
    handleNameInput(event) {
      const value = event.target.value.toLowerCase();
      if (this.selectedType === 'template') {
        this.form.name = value.replace(/[^a-zA-Z0-9_]/g, "");
      } else if (this.selectedType === 'carousel') {
        this.cardform.name = value.replace(/[^a-zA-Z0-9_]/g, "");
      } else if (this.selectedType === 'flow') {
        this.flowform.name = value.replace(/[^a-zA-Z0-9_]/g, "");
      }
    },
    addUnderscore(event) {
      event.preventDefault();
      if (this.selectedType === 'template') {
        this.form.name += "_";
      } else if (this.selectedType === 'carousel') {
        this.cardform.name += "_";
      } else if (this.selectedType === 'flow') {
        this.flowform.name += "_";
      }
    },
    updateBodyExamples(value) {
      if (this.selectedType === 'template') {
        this.form.body.example = value;
      }
    },
    updateHeaderExamples(value) {
      this.form.header.example = value;
      this.form.header.file_url = URL.createObjectURL(value);

    },
    handleSubmitFlowTemplate() {
      console.log("Submitted Flow JSON:", this.flowform.flowJson);
    },
  },
};
</script>

<script setup>
import axios from "axios";
import AppLayout from "./../Layout/App.vue";
import FormInput from "@/Components/FormInput.vue";
import FormSelect from "@/Components/FormSelect.vue";
import FormTextArea from "@/Components/FormTextArea.vue";
import FormToggleSwitch from "@/Components/FormToggleSwitch.vue";
import BodyTextArea from "@/Components/Template/BodyTextArea.vue";
import HeaderTextArea from "@/Components/Template/HeaderTextArea.vue";
import { ref, computed, watch } from "vue";
import { Link } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import "vue3-toastify/dist/index.css";
import { router } from "@inertiajs/vue3";

// Import Lucide Icons
import {
  FileText,
  Info,
  ArrowLeft,
  Send,
  Loader2,
  MessageSquare,
  Zap,
  Layers,
  Workflow,
  Settings,
  ImageIcon,
  Video,
  FileIcon,
  AlignLeft,
  Phone,
  Link2,
  Copy,
  Trash2,
  Plus,
  Shield,
  Smartphone,
  Clock,
  MousePointerClick,
  Upload,
  CheckCircle,
  ExternalLink,
  X,
  Reply,
  SquareArrowOutUpRight,
} from "lucide-vue-next";

const props = defineProps(["languages", "settings"]);
const headerCharacterLimit = ref("60");
const headerCharacterCount = ref("0");
const bodyCharacterLimit = ref("1098");
const bodyCharacterCount = ref("0");
const footerCharacterLimit = ref("60");
const footerCharacterCount = ref("0");
const isLoading = ref(false);
const imageUrl = ref(null);
const isModalOpen = ref(false);
const error = ref(null);
const selectedType = ref("template");

const form = ref({
  name: null,
  category: "UTILITY",
  language: null,
  message_send_ttl_seconds: null,
  customize_ttl: false,
  header: {
    format: "TEXT",
    text: null,
    example: [],
  },
  body: {
    text: null,
    variables: null,
    add_security_recommendation: false,
    example: [],
  },
  footer: {
    text: null,
    code_expiration_minutes: 5,
  },
  code_expiration: false,
  set_custom_validity_period: false,
  buttons: [],
  authentication_button: {
    type: "OTP",
    otp_type: "zero_tap",
    text: "Copy Code",
    autofill_text: "Autofill",
    zero_tap_terms_accepted: true,
    supported_apps: [
      {
        package_name: null,
        signature_hash: null,
      },
    ],
  },
});

const cardform = ref({
  name: "",
  language: "",
  category: "",
  body: {
    text: "",
  },
});

const flowform = ref({
  name: "",
  language: "",
  category: "",
  body: {
    text: "",
  },
  buttonText: "",
  flowJson: "",
});

const cards = ref([]);
const bodyExamplesInput = ref("");
const uploadingIndex = ref(null);
const showFlowBuilder = ref(false);

const config = ref(props.settings.metadata);
const settings = ref(config.value ? JSON.parse(config.value) : null);

const formattedMessage = ref("");

const formatContent = (text) => {
  const boldRegex = /\*(.*?)\*/g;
  const italicRegex = /_(.*?)_/g;
  const strikethroughRegex = /~(.*?)~/g;
  const monospaceRegex = /```(.*?)```/g;

  return text
    .replace(boldRegex, "<b>$1</b>")
    .replace(italicRegex, "<i>$1</i>")
    .replace(strikethroughRegex, "<del>$1</del>")
    .replace(monospaceRegex, "<code>$1</code>");
};

watch(
  () => form.value.body.text,
  (newValue) => {
    formattedMessage.value = newValue ? formatContent(newValue) : null;
  }
);

const langOptions = ref(props.languages);
const categoryOptions = ref([
  { value: "UTILITY", label: "Utility" },
  { value: "MARKETING", label: "Marketing" },
  { value: "AUTHENTICATION", label: "Authentication" },
]);

const categoryCrouselOptions = ref([
  { value: "MARKETING", label: "Marketing" },
]);

const previousExamples = ref({});

const changeHeaderType = (value) => {
  const currentType = form.value.header.format;

  if (
    !(currentType in previousExamples.value) ||
    JSON.stringify(previousExamples.value[currentType]) !==
    JSON.stringify(form.value.header.example)
  ) {
    previousExamples.value[currentType] = form.value.header.example;
  }

  form.value.header.format = value;

  if (previousExamples.value[value] !== undefined) {
    form.value.header.example = previousExamples.value[value];
    form.value.header.file_url = URL.createObjectURL(previousExamples.value[value]);

  } else {
    form.value.header.example = null;
    form.value.header.file_url = null;
  }
};

const handleFileUpload = (event) => {
  const file = event.target.files[0];
  const reader = new FileReader();
  reader.onload = (e) => {
    imageUrl.value = e.target.result;
    form.value.header.example = file;
    form.value.header.file_url = URL.createObjectURL(file);
  };
  reader.readAsDataURL(file);
};

const removeFile = () => {
  form.value.header.example = "";
  form.value.header.file_url = null;

};

const characterCount = (type) => {
  let limit = 0;
  let count = 0;
  switch (type) {
    case "header":
      limit = headerCharacterLimit.value;
      count = form.value.header.text.length;
      if (count <= limit) {
        headerCharacterCount.value = count;
      } else {
        form.value.header.text = form.value.header.text.slice(0, limit);
        headerCharacterCount.value = limit;
      }
      break;

    case "body":
      limit = bodyCharacterLimit.value;
      count = form.value.body.text.length;
      if (count <= limit) {
        bodyCharacterCount.value = count;
      } else {
        form.value.body.text = form.value.body.text.slice(0, limit);
        bodyCharacterCount.value = limit;
      }
      break;

    case "footer":
      limit = footerCharacterLimit.value;
      count = form.value.footer.text.length;
      if (count <= limit) {
        footerCharacterCount.value = count;
      } else {
        form.value.footer.text = form.value.footer.text.slice(0, limit);
        footerCharacterCount.value = limit;
      }
      break;
  }
};

const addButton = ($type) => {
  if ($type === "call") {
    const buttonsCount = form.value.buttons.filter(
      (button) => button.type === "PHONE_NUMBER"
    ).length;

    if (buttonsCount < 1) {
      form.value.buttons.push({
        type: "PHONE_NUMBER",
        country: null,
        text: null,
        phone_number: null,
      });
    }
  } else if ($type === "website") {
    const buttonsCount = form.value.buttons.filter(
      (button) => button.type === "URL"
    ).length;

    if (buttonsCount < 2) {
      form.value.buttons.push({
        type: "URL",
        text: null,
        url: null,
      });
    }
  } else if ($type === "custom") {
    const buttonsCount = form.value.buttons.filter(
      (button) => button.type === "QUICK_REPLY"
    ).length;

    if (buttonsCount < 6) {
      form.value.buttons.push({
        type: "QUICK_REPLY",
        text: null,
      });
    }
  } else if ($type === "offer") {
    const buttonsCount = form.value.buttons.filter(
      (button) => button.type === "copy_code"
    ).length;

    if (buttonsCount < 1) {
      form.value.buttons.push({
        type: "copy_code",
        example: null,
      });
    }
  }
};

const addSupportedApp = () => {
  const appsCount = form.value.authentication_button.supported_apps.length;

  if (appsCount < 5) {
    form.value.authentication_button.supported_apps.push({
      package_name: null,
      signature_hash: null,
    });
  }
};

const removeSupportedApp = (index) => {
  form.value.authentication_button.supported_apps.splice(index, 1);
};

const removeButton = (index) => {
  if (index >= 0 && index < form.value.buttons.length) {
    form.value.buttons.splice(index, 1);
  }
};

const isFormValid = computed(() => {
  if (
    form.value.name === null ||
    form.value.name.trim() === "" ||
    form.value.language === null ||
    form.value.language.trim() === "" ||
    form.value.category === null ||
    form.value.category.trim() === "" ||
    form.value.buttons.some((button) => {
      return (
        button.name === null ||
        button.name === "" ||
        button.type === null ||
        button.type === "" ||
        button.country === null ||
        button.country === "" ||
        button.text === null ||
        button.text === "" ||
        button.phone_number === null ||
        button.phone_number === ""
      );
    })
  ) {
    return false;
  }

  if (form.value.category !== "AUTHENTICATION") {
    if (form.value.body.text === null || form.value.body.text.trim() === "") {
      return false;
    }

    if (form.value.body.example.length > 0) {
      const allKeysHaveValues = Object.keys(form.value.body.example).every(
        (key) => {
          const value = form.value.body.example[key];
          return value !== undefined && value !== null && value !== "";
        }
      );

      if (!allKeysHaveValues) {
        return false;
      }
    }

    if (
      form.value.header.example.length > 0 &&
      !Object.keys(form.value.header.example).every((key) => {
        const value = form.value.header.example[key];
        return value !== undefined && value !== null && value !== "";
      })
    ) {
      return false;
    }
  }

  if (form.value.category === "AUTHENTICATION") {
    const authButton = form.value.authentication_button;

    if (!authButton.otp_type || authButton.otp_type.trim() === "") {
      return false;
    }

    if (
      authButton.otp_type === "copy_code" ||
      authButton.otp_type === "one_tap" ||
      authButton.otp_type === "zero_tap"
    ) {
      if (!authButton.text || authButton.text.trim() === "") {
        return false;
      }
    }

    if (
      (authButton.otp_type === "one_tap" ||
        authButton.otp_type === "zero_tap") &&
      (!authButton.autofill_text || authButton.autofill_text.trim() === "")
    ) {
      return false;
    }

    if (["one_tap", "zero_tap"].includes(authButton.otp_type)) {
      if (
        !Array.isArray(authButton.supported_apps) ||
        authButton.supported_apps.length === 0
      ) {
        return false;
      }

      const allAppsValid = authButton.supported_apps.every((app) => {
        return (
          app.package_name &&
          app.package_name.trim() !== "" &&
          app.signature_hash &&
          app.signature_hash.trim() !== ""
        );
      });

      if (!allAppsValid) {
        return false;
      }
    }
  }

  return true;
});

const submitForm = () => {
  console.log("Submitting form");
  isLoading.value = true;
  isModalOpen.value = true;
  axios
    .post("/templates/create", form.value, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    })
    .then((response) => {
      if (response.data.success === false) {
        isLoading.value = false;
        error.value = response.data.message || "Internal server error";;
      } else {
        router.visit("/templates", {
          method: "get",
        });
      }
    })
    .catch((err) => {
      error.value = err.response.data.error || "Internal server error";
      console.error("Submit error:", err);
      isLoading.value = false;
    });
};

const codeDeliveryOptions = [
  {
    value: "zero_tap",
    label: "Zero-tap auto-fill",
    description:
      "This is recommended as the easiest option for your customers. Zero-tap will automatically send code without requiring your customer to tap a button.",
  },
  {
    value: "one_tap",
    label: "One-tap auto-fill",
    description:
      "The code sends to your app when customers tap the button. A copy code message will be sent if auto-fill isn't possible.",
  },
  {
    value: "copy_code",
    label: "Copy code",
    description:
      "Basic authentication with quick setup. Your customers copy and paste the code into your app.",
  },
];

const authTTLOptions = ref([
  { value: "30", label: "30 seconds" },
  { value: "60", label: "1 minute" },
  { value: "120", label: "2 minutes" },
  { value: "180", label: "3 minutes" },
  { value: "300", label: "5 minutes" },
  { value: "600", label: "10 minutes" },
  { value: "800", label: "15 minutes" },
]);

const utilityTTLOptions = ref([
  { value: "30", label: "30 seconds" },
  { value: "60", label: "1 minute" },
  { value: "120", label: "2 minutes" },
  { value: "300", label: "5 minutes" },
  { value: "600", label: "10 minutes" },
  { value: "800", label: "15 minutes" },
  { value: "1600", label: "30 minutes" },
  { value: "3200", label: "1 hour" },
  { value: "9600", label: "3 hours" },
  { value: "19200", label: "6 hours" },
  { value: "38400", label: "12 hours" },
]);

function formatText(text) {
  return text
    .toLowerCase()
    .replace(/_/g, " ")
    .replace(/^\w/, (c) => c.toUpperCase());
}

const closeModal = () => {
  isModalOpen.value = false;
  setTimeout(() => {
    error.value = null;
  }, 500);
};

// Carousel Functions
function addCard() {
  if (cards.value.length < 10) {
    cards.value.push({
      components: [
        {
          type: "header",
          format: "image",
          example: {
            header_handle: [""],
          },
        },
        {
          type: "body",
          text: "",
        },
        {
          type: "buttons",
          buttons: [],
        },
      ],
    });
  }
}

function removeCard(index) {
  cards.value.splice(index, 1);
}

function addCardButton(cardIndex) {
  const buttons = cards.value[cardIndex].components[2].buttons;
  if (buttons.length < 3) {
    buttons.push({
      type: "quick_reply",
      text: "",
      url: "",
      example: [""],
    });
  }
}

function removeCardButton(cardIndex, buttonIndex) {
  cards.value[cardIndex].components[2].buttons.splice(buttonIndex, 1);
}

async function onFileChange(e, cardIndex) {
  const selectedFile = e.target.files[0];
  if (!selectedFile || !cards.value[cardIndex]) return;

  uploadingIndex.value = cardIndex;

  const formData = new FormData();
  formData.append("image", selectedFile);

  try {
    const res = await axios.post("/meta-upload", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    cards.value[cardIndex].components[0].example.header_handle[0] =
      res.data.file_handle;
    cards.value[cardIndex].image_url = URL.createObjectURL(selectedFile);
  } catch (err) {
    console.error("Upload failed:", err.response?.data || err.message);
    alert("Upload failed. Check console for error.");
  } finally {
    uploadingIndex.value = null;
  }
}

const cleanedCards = computed(() => {
  return cards.value.map((card) => {
    const cleanedComponents = card.components.map((component) => {
      if (component.type === "buttons") {
        return {
          ...component,
          buttons: component.buttons.map((btn) => {
            if (btn.type === "quick_reply") {
              return {
                type: btn.type,
                text: btn.text,
              };
            } else {
              return btn;
            }
          }),
        };
      }
      return component;
    });
    return {
      components: cleanedComponents,
    };
  });
});

function CardsubmitForm() {
  for (let i = 0; i < cards.value.length; i++) {
    const card = cards.value[i];

    const headerHandle = card.components[0].example.header_handle[0];
    if (!headerHandle || headerHandle.trim() === "") {
      alert(`❌ Card ${i + 1}: Header image must be uploaded.`);
      return;
    }

    const bodyText = card.components[1].text;
    if (!bodyText || bodyText.trim() === "") {
      alert(`❌ Card ${i + 1}: Body text is required.`);
      return;
    }

    const buttons = card.components[2].buttons;
    if (buttons.length === 0) {
      alert(`❌ Card ${i + 1}: At least one button is required.`);
      return;
    }

    for (let j = 0; j < buttons.length; j++) {
      const btn = buttons[j];
      if (!btn.text || btn.text.trim() === "") {
        alert(`❌ Card ${i + 1}, Button ${j + 1}: Button text is required.`);
        return;
      }
      if (btn.type === "url") {
        if (!btn.url || btn.url.trim() === "") {
          alert(`❌ Card ${i + 1}, Button ${j + 1}: URL is required.`);
          return;
        }
        if (!btn.example[0] || btn.example[0].trim() === "") {
          alert(`❌ Card ${i + 1}, Button ${j + 1}: Example is required.`);
          return;
        }
      }
    }
  }

  if (!cardform.value.body.text.trim()) {
    alert("❌ Body text (outside cards) is required.");
    return;
  }

  isLoading.value = true;
  isModalOpen.value = true;

  const exampleValues = bodyExamplesInput.value
    .split(",")
    .map((val) => val.trim())
    .filter((val) => val);

  const finalJson = {
    name: `crousel_${cardform.value.name}`,
    language: cardform.value.language,
    category: cardform.value.category,
    components: [
      {
        type: "body",
        text: cardform.value.body.text,
        example: {
          body_text: [exampleValues],
        },
      },
      {
        type: "carousel",
        cards: cleanedCards.value,
      },
    ],
  };

  axios
    .post("/crousel/create", finalJson, {
      headers: {
        "Content-Type": "application/json",
      },
    })
    .then((response) => {
      if (response.data.success === false) {
        isLoading.value = false;
        error.value = response.data.message || "Internal server error";;
      } else {
        isLoading.value = false;
        window.location.href = "/templates";
      }
    })
    .catch((err) => {
      error.value = err.response.data.error || "Internal server error";
      console.error("Submit error:", err);
      isLoading.value = false;
    });
}

function FlowSubmitForm() {
  if (!flowform.value.body.text.trim()) {
    alert("❌ Body text is required.");
    return;
  }
  if (!flowform.value.name.trim()) {
    alert("❌ Name is required.");
    return;
  }
  if (!flowform.value.language.trim()) {
    alert("❌ Please Select language.");
    return;
  }
  if (!flowform.value.category.trim()) {
    alert("❌ Please Select Category.");
    return;
  }
  if (!flowform.value.buttonText.trim()) {
    alert("❌ Button text is required.");
    return;
  }
  if (!flowform.value.flowJson.trim()) {
    alert("❌ Flowjson is required.");
    return;
  }

  isLoading.value = true;
  isModalOpen.value = true;

  const finalFlowJson = {
    name: `flow_${flowform.value.name}`,
    language: flowform.value.language,
    category: flowform.value.category,
    components: [
      {
        type: "body",
        text: flowform.value.body.text,
      },
      {
        type: "buttons",
        buttons: [
          {
            type: "FLOW",
            text: flowform.value.buttonText,
            flow_json: flowform.value.flowJson,
          },
        ],
      },
    ],
  };

  axios
    .post("/crousel/create", finalFlowJson, {
      headers: {
        "Content-Type": "application/json",
      },
    })
    .then((response) => {
      if (response.data.success === false) {
        isLoading.value = false;
        error.value = response.data.message || "Internal server error";;
      } else {
        isLoading.value = false;
        window.location.href = "/templates";
      }
    })
    .catch((err) => {
      error.value = err.response.data.error || "Internal server error";
      console.error("Submit error:", err);
      isLoading.value = false;
    });
}

watch(form, () => {
  isFormValid.value;
});
</script>


<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

* {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Smooth Animations */
.animate-in {
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.zoom-in {
  animation: zoomIn 0.3s ease-out;
}

@keyframes zoomIn {
  from {
    opacity: 0;
    transform: scale(0.9);
  }

  to {
    opacity: 1;
    transform: scale(1);
  }
}

/* Focus States */
input:focus,
textarea:focus,
select:focus {
  outline: none;
  border-color: #ff5100;
}

/* Button Hover Effects */
button {
  transition: all 0.2s ease-in-out;
}

button:active {
  transform: scale(0.98);
}

/* Card Hover Effects */
.bg-white {
  transition: all 0.3s ease;
}

/* Loading Spinner */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Pulse Animation */
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

/* Gradient Background Animation */
@keyframes gradient {
  0% {
    background-position: 0% 50%;
  }

  50% {
    background-position: 100% 50%;
  }

  100% {
    background-position: 0% 50%;
  }
}

.bg-gradient-to-br,
.bg-gradient-to-r {
  background-size: 200% 200%;
  animation: gradient 3s ease infinite;
}

/* Smooth Transitions */
* {
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

/* WhatsApp Message Bubble */
.speech-bubble-left::before {
  content: '';
  position: absolute;
  left: -8px;
  top: 0;
  width: 0;
  height: 0;
  border-style: solid;
  border-width: 0 8px 8px 0;
  border-color: transparent white transparent transparent;
}

/* Card Shadow on Hover */
.hover\:shadow-xl:hover {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Backdrop Blur */
.backdrop-blur-sm {
  backdrop-filter: blur(8px);
}

/* Disable Text Selection on Buttons */
button {
  user-select: none;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
}
</style>
