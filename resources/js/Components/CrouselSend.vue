<!-- <script setup>
import axios from "axios";
import FormInput from "@/Components/FormInput.vue";
import FormSelect from "@/Components/FormSelect.vue";
import WhatsappTemplate from "@/Components/WhatsappTemplate.vue";
import { ref, onMounted } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import "vue3-toastify/dist/index.css";
import { trans } from "laravel-vue-i18n";
import { router } from "@inertiajs/vue3";

const props = defineProps({
  templates: Object,
  contactGroups: Object,
  settings: Array,
  contact: {
    type: String,
    default: null,
  },
  displayTitle: {
    type: Boolean,
    default: false,
  },
  displayCancelBtn: {
    type: Boolean,
    default: true,
  },
  isCampaignFlow: {
    type: Boolean,
    default: true,
  },
  sendText: {
    type: String,
    default: "Save",
  },

  // ✅ Additional props passed from CampaignForm.vue
  form: Object,
  settingsParsed: Object,
  isLoading: Boolean,
  contactGroupOptions: Array,
  templateOptions: Array,
  variableOptions: Array,
  dynamicOptions: Array,
});

const isTempLoading = ref(false);
const isLoading = ref(false);

const contactGroupOptions = ref([
  { value: "all", label: trans("all contacts") },
]);
const templateOptions = ref([]);
const config = ref(props.settings?.metadata);
const settings = ref(config.value ? JSON.parse(config.value) : null);

const variableOptions = ref([
  { value: "static", label: trans("static") },
  { value: "dynamic", label: trans("dynamic") },
]);

const dynamicOptions = ref([
  { value: "first name", label: trans("contact first name") },
  { value: "last name", label: trans("contact last name") },
  { value: "name", label: trans("contact full name") },
  { value: "phone", label: trans("contact phone") },
  { value: "email", label: trans("contact email") },
]);

const form = useForm({
  name: null,
  template: null,
  contacts: null,
  time: null,
  skip_schedule: false,
  header: {
    format: null,
    text: null,
    parameters: [],
  },
  body: {
    text: null,
    parameters: [],
  },
  footer: {
    text: null,
  },
  buttons: [],
  carousel: {
    cards: [],
  },
});

const loadTemplate = async () => {
  try {
    const response = await axios.get("/templates/" + form.template);
    if (response) {
      console.log("Template data fetched successfully:", response.data);
      const metadata = JSON.parse(response.data.metadata);
      console.log("Template Metadata from GET api:", metadata);
      form.header.format = extractComponent(metadata, "HEADER", "format");
      form.header.text = extractComponent(metadata, "HEADER", "text");
      const headerExamples = extractComponent(metadata, "HEADER", "example");
      if (headerExamples) {
        if (form.header.format === "TEXT") {
          form.header.parameters = headerExamples.header_text.map((item) => ({
            type: "text",
            selection: "static",
            value: item,
          }));
        } else if (
          ["IMAGE", "DOCUMENT", "VIDEO"].includes(form.header.format)
        ) {
          form.header.parameters = headerExamples.header_handle.map(() => ({
            type: form.header.format,
            selection: "upload",
            value: null,
            url: null,
            fileName: null,
          }));
        }
      } else {
        form.header.parameters = [];
      }

      form.body.text = extractComponent(metadata, "BODY", "text");
      const bodyExamples = extractComponent(metadata, "BODY", "example");
      if (bodyExamples) {
        form.body.parameters = bodyExamples.body_text[0].map((item) => ({
          type: "text",
          selection: "static",
          value: item,
        }));
      } else {
        form.body.parameters = [];
      }

      form.footer.text = extractComponent(metadata, "FOOTER", "text");

      const buttons = extractComponent(metadata, "BUTTONS", "buttons");
      if (buttons) {
        form.buttons = buttons.map((item) => ({
          type: item.type,
          text: item.text,
          value: item[item.type.toLowerCase()] ?? null,
          parameters:
            item.type === "QUICK_REPLY"
              ? [{ type: "static", value: null }]
              : item.example
                ? item.example.map((param) => ({ type: "static", value: param }))
                : [],
        }));
      } else {
        form.buttons = [];
      }

      const carousel = metadata.components.find((c) => c.type === "CAROUSEL");
      if (carousel) {
        form.carousel.cards = carousel.cards.map((card) => {
          const cardHeader = card.components.find((c) => c.type === "HEADER");
          const cardBody = card.components.find((c) => c.type === "BODY");
          const cardButtons = card.components.find((c) => c.type === "BUTTONS");

          return {
            header: {
              format: cardHeader?.format || null,
              parameters: cardHeader?.example?.header_handle
                ? cardHeader.example.header_handle.map(() => ({
                  type: cardHeader.format,
                  selection: "upload",
                  value: null,
                  url: null,
                  fileName: null,
                }))
                : [],
            },
            body: {
              text: cardBody?.text || null,
              parameters: cardBody?.example?.body_text?.[0]
                ? cardBody.example.body_text[0].map((item) => ({
                  type: "text",
                  selection: "static",
                  value: item,
                }))
                : [],
            },
            buttons: cardButtons?.buttons
              ? cardButtons.buttons.map((button) => ({
                type: button.type,
                text: button.text,
                value: button[button.type.toLowerCase()] ?? null,
                parameters:
                  button.type === "QUICK_REPLY"
                    ? [{ type: "static", value: null }]
                    : button.example
                      ? button.example.map((param) => ({
                        type: "static",
                        value: param,
                      }))
                      : [],
              }))
              : [],
          };
        });
      } else {
        form.carousel.cards = [];
      }
    }
  } catch (error) {
    console.error("Error fetching data:", error);
  }
};

const uploadMedia = async (file, type) => {
  const formData = new FormData();
  formData.append("file", file);
  formData.append("type", type.toLowerCase());
  formData.append("messaging_product", "whatsapp");

  try {
    const response = await axios.post("/upload-file", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });
    console.log("Media uploaded successfully:", response.data);
    return response.data.public_url;
  } catch (error) {
    console.error("Error uploading media:", error);
    throw error;
  }
};

const handleFileUpload = async (event, cardIndex = null) => {
  const fileType =
    cardIndex !== null
      ? form.carousel.cards[cardIndex].header.parameters[0].type
      : form.header.parameters[0].type;

  const fileSizeLimit = getFileSizeLimit(fileType);
  const file = event.target.files[0];
  isTempLoading.value = true;
  if (file && file.size > fileSizeLimit) {
    alert(
      trans("file size exceeds the limit. Max allowed size:") +
      " " +
      fileSizeLimit +
      "b"
    );
    event.target.value = null;
    return;
  }

  try {
    const url = await uploadMedia(file, fileType);
    console.log("File uploaded successfully:", url);
    if (cardIndex !== null) {
      form.carousel.cards[cardIndex].header.parameters[0].url = url;
      form.carousel.cards[cardIndex].header.parameters[0].fileName = file.name;
    } else {
      form.header.parameters[0].url = url;
      form.header.parameters[0].fileName = file.name;
    }
  } catch (error) {
    alert(trans("Failed to upload file"));
    event.target.value = null;
  } finally {
    isTempLoading.value = false;
  }
};

const getFileAcceptAttribute = (fileType) => {
  switch (fileType) {
    case "IMAGE":
      return ".png, .jpg";
    case "DOCUMENT":
      return ".pdf, .txt, .ppt, .doc, .xls, .docx, .pptx, .xlsx";
    case "VIDEO":
      return ".mp4";
    default:
      return "";
  }
};

const getFileSizeLimit = (fileType) => {
  switch (fileType) {
    case "IMAGE":
      return 5 * 1024 * 1024; // 5MB
    case "DOCUMENT":
      return 100 * 1024 * 1024; // 100MB
    case "VIDEO":
      return 16 * 1024 * 1024; // 16MB
    default:
      return Infinity;
  }
};

const extractComponent = (data, type, customProperty) => {
  const component = data.components.find((c) => c.type === type);
  return component ? component[customProperty] : null;
};

const transformOptions = (options) => {
  return options.map((option) => ({
    value: option.uuid,
    label: option.language
      ? option.name + " [" + option.language + "]"
      : option.name,
  }));
};

const submitForm = () => {
  isLoading.value = true;

  const url = props.isCampaignFlow
    ? "/campaigns"
    : `/chat/${props.contact}/send/template`;
  const payload = form.data();
  console.log(props.isCampaignFlow ? "Campaign Flow" : "Single Contact Flow");

  console.log("POST Request:", JSON.stringify({ url, payload }, null, 2));

  form.post(url, {
    onFinish: () => {
      isLoading.value = false;
      if (!props.isCampaignFlow) {
        emit("viewTemplate", false);
      }
    },
  });
};

const submitCarouselForm = async () => {
  isLoading.value = true;

  const payload = {
    messaging_product: "whatsapp",
    recipient_type: "individual",
    to: props.isCampaignFlow ? form.contacts : props.contact,
    type: "template",
    template: {
      name: form.template,
      language: {
        code: "en",
      },
      components: [],
    },
  };

  if (form.carousel.cards.length > 0) {
    const carouselComponent = {
      type: "carousel",
      cards: form.carousel.cards.map((card, index) => ({
        card_index: index,
        components: [
          {
            type: "header",
            parameters: card.header.parameters.map((param) => ({
              type: param.type.toLowerCase(),
              [param.type.toLowerCase()]: {
                link: param.url,
              },
            })),
          },

        ],
      })),
    };
    payload.template.components.push(carouselComponent);
  }

  if (form.header.format && form.header.parameters.length > 0) {
    payload.template.components.push({
      type: "header",
      parameters: form.header.parameters.map((param) => ({
        type: param.type.toLowerCase(),
        [param.type.toLowerCase()]: {
          id: param.url,
        },
      })),
    });
  }

  if (form.body.text) {
    payload.template.components.push({
      type: "body",
      parameters: form.body.parameters.map((param) => ({
        type: "text",
        text: param.selection === "static" ? param.value : `{{${param.value}}}`,
      })),
    });
  }

  if (form.footer.text) {
    payload.template.components.push({
      type: "footer",
      text: form.footer.text,
    });
  }

  if (form.buttons.length > 0) {
    payload.template.components.push({
      type: "buttons",
      buttons: form.buttons.map((button) => ({
        type: button.type,
        text: button.text,
        ...(button.parameters.length > 0
          ? {
            parameters: button.parameters.map((param) => ({
              type: "text",
              text:
                param.type === "static" ? param.value : `{{${param.value}}}`,
            })),
          }
          : {}),
      })),
    });
  }

  try {
    const url = "/send/crousel";

    console.log(
      "POST Request:",
      JSON.stringify(
        {
          url,
          payload,
          campaign_name: form.name,
        },
        null,
        2
      )
    );

    const res = await axios.post(url, {
      payload,
      campaign_name: form.name,
    });
    console.log("Response:", res.data);
    if (!props.isCampaignFlow) {
      emit("viewTemplate", false);
    }
  } catch (error) {
    console.error("Error sending carousel message:", error);
    alert(trans("Failed to send carousel message"));
  } finally {
    isLoading.value = false;
  }
};

const handleSubmit = async () => {
  if (form.carousel.cards.length > 0) {
    await submitCarouselForm();
  } else {
    await submitForm();
  }
  router.visit("/campaigns");
};

const emit = defineEmits(["viewTemplate"]);

const viewTemplate = () => {
  emit("viewTemplate", false);
};


onMounted(() => {
  // Filter templates with names starting with "carousel_"
  const filteredTemplates = props.templates.filter(template =>
    template.name?.startsWith('crousel_')
  );

  templateOptions.value = transformOptions(filteredTemplates);

  contactGroupOptions.value = [
    ...contactGroupOptions.value,
    ...transformOptions(props.contactGroups)
  ];
});


</script>

<template>
  <div :class="'md:flex md:flex-grow-1'">
    <form @submit.prevent="handleSubmit()" class="overflow-y-auto md:w-[50%]"
      :class="isCampaignFlow ? 'p-4 md:p-8 h-[90vh]' : ' h-full'">
      <div v-if="displayTitle" class="m-1 rounded px-3 pt-3 pb-3 bg-slate-100 flex items-center justify-between mb-4">
        <h3 class="text-[15px]">{{ $t("Send Template Message") }}</h3>
        <button @click="viewTemplate()"
          class="text-sm md:inline-flex hidden justify-center rounded-md border border-transparent bg-red-800 px-4 py-1 text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
          Cancel
        </button>
      </div>
      <div class="grid gap-x-6 gap-y-4 mb-8" :class="isCampaignFlow ? 'sm:grid-cols-6' : 'p-3 md:p-3'">
        <FormInput v-if="isCampaignFlow" v-model="form.name" :name="$t('Campaign name')" :type="'text'"
          :error="form.errors.name" :required="true" :class="'sm:col-span-6'" />
        <FormSelect v-model="form.template" @update:modelValue="loadTemplate" :options="templateOptions"
          :required="true" :error="form.errors.template" :name="$t('Template')" :class="'sm:col-span-3'"
          :placeholder="$t('Select template')" />
        <FormSelect v-if="isCampaignFlow" v-model="form.contacts" :options="contactGroupOptions" :name="$t('Send to')"
          :required="true" :class="'sm:col-span-3'" :placeholder="$t('Select contacts')"
          :error="form.errors.contacts" />
        <FormInput v-if="isCampaignFlow && !form.skip_schedule" v-model="form.time" :name="$t('Scheduled time')"
          :type="'datetime-local'" :error="form.errors.time" :required="true" :class="'sm:col-span-6'" />
        <div v-if="isCampaignFlow" class="relative flex gap-x-3 sm:col-span-6">
          <div class="flex h-6 items-center">
            <input v-model="form.skip_schedule" id="skip-schedule" name="skip-schedule" type="checkbox"
              class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
          </div>
          <div class="text-sm leading-6">
            <label for="skip-schedule" class="font-medium text-gray-900">{{
              $t("Skip scheduling & send immediately")
            }}</label>
          </div>
        </div>
      </div>
      <div :class="isCampaignFlow ? '' : 'px-3 md:px-3'">
        <div v-if="form.header.parameters.length > 0" class="bg-slate-100 p-3 mt-4 text-sm">
          <h2 class="flex items-center justify-between space-x-2 pb-2 border-b">
            <div class="flex items-center space-x-2">
              <span>{{ $t("Header variables") }}</span>
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 1024 1024">
                <path fill="currentColor"
                  d="M512 64a448 448 0 1 1 0 896.064A448 448 0 0 1 512 64zm67.2 275.072c33.28 0 60.288-23.104 60.288-57.344s-27.072-57.344-60.288-57.344c-33.28 0-60.16 23.104-60.16 57.344s26.88 57.344 60.16 57.344zM590.912 699.2c0-6.848 2.368-24.64 1.024-34.752l-52.608 60.544c-10.88 11.456-24.512 19.392-30.912 17.28a12.992 12.992 0 0 1-8.256-14.72l87.68-276.992c7.168-35.136-12.544-67.2-54.336-71.296c-44.096 0-108.992 44.736-148.48 101.504c0 6.784-1.28 23.68.064 33.792l52.544-60.608c10.88-11.328 23.552-19.328 29.952-17.152a12.8 12.8 0 0 1 7.808 16.128L388.48 728.576c-10.048 32.256 8.96 63.872 55.04 71.04c67.84 0 107.904-43.648 147.456-100.416z" />
              </svg>
            </div>
            <span>
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                <path fill="currentColor"
                  d="M6.102 16.98c-1.074 0-1.648-1.264-.94-2.073l5.521-6.31a1.75 1.75 0 0 1 2.634 0l5.522 6.31c.707.809.133 2.073-.94 2.073H6.101Z" />
              </svg>
            </span>
          </h2>
          <div v-for="(item, index) in form.header.parameters" :key="index" class="mt-2 flex items-center space-x-4">
            <div v-if="form.header.parameters[index].type === 'text'" class="w-full">
              <FormSelect v-model="form.header.parameters[index].selection" :name="$t('Content type')"
                :options="variableOptions" :class="'sm:col-span-6'" />
            </div>
            <div v-if="form.header.parameters[index].type === 'text'" class="w-full">
              <FormInput v-if="form.header.parameters[index].selection === 'static'" :name="$t('Value')"
                :required="true" :error="form.errors['header.parameters.0.value']"
                v-model="form.header.parameters[index].value" :type="'text'" :class="'sm:col-span-6'" />
              <FormSelect v-if="form.header.parameters[index].selection === 'dynamic'" :name="$t('Value')"
                :required="true" :error="form.errors['header.parameters.0.value']"
                v-model="form.header.parameters[index].value" :options="dynamicOptions" :class="'sm:col-span-6'" />
            </div>
            <div v-if="
              ['IMAGE', 'DOCUMENT', 'VIDEO'].includes(
                form.header.parameters[index].type
              )
            " class="w-full mt-3">
              <div class="flex items-center space-x-4">
                <label
                  class="cursor-pointer flex justify-center px-2 py-2 w-[30%] bg-slate-200 shadow-sm rounded-lg border"
                  :class="form.errors['header.parameters.0.value']
                    ? 'border border-red-700'
                    : ''
                    " for="file-upload">
                  {{ $t("Upload") }}
                </label>
                <input type="file" class="sr-only" :accept="getFileAcceptAttribute(form.header.parameters[index].type)
                  " ref="fileInput" id="file-upload" @change="handleFileUpload" />
                <div v-if="form.header.parameters[index].fileName" class="w-[20em] truncate">
                  {{ form.header.parameters[index].fileName }}
                  <div v-if="isTempLoading" class="flex items-center justify-center min-h-screen">
                    <Loading v-if="isTempLoading" />
                  </div>
                </div>
                <span v-else>{{ $t("No file chosen") }}</span>
              </div>
              <p v-if="form.header.parameters[index].type === 'IMAGE'" class="text-left text-xs mt-2">
                {{ $t("Max file upload size is") }} <b>5MB</b> <br />
                {{ $t("Supported file extensions") }}: .png, .jpg
              </p>
              <p v-if="form.header.parameters[index].type === 'DOCUMENT'" class="text-left text-xs mt-2">
                {{ $t("Max file upload size is") }} <b>100MB</b> <br />
                {{ $t("Supported file extensions") }}: .pdf, .txt, .ppt, .doc,
                .xls, .docx, .pptx, .xlsx
              </p>
              <p v-if="form.header.parameters[index].type === 'VIDEO'" class="text-left text-xs mt-2">
                {{ $t("Max file upload size is") }} <b>16MB</b> <br />
                {{ $t("Supported file extensions") }}: .mp4
              </p>
              <div v-if="form.errors['header.parameters.0.value']" class="form-error text-[#b91c1c] text-xs">
                {{ form.errors["header.parameters.0.value"] }}
              </div>
            </div>
          </div>
        </div>
        <div v-if="form.body.parameters.length > 0" class="bg-slate-100 p-3 mt-1 text-sm">
          <h2 class="flex items-center justify-between space-x-2 pb-2 border-b">
            <div class="flex items-center space-x-2">
              <span>{{ $t("Body variables") }}</span>
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 1024 1024">
                <path fill="currentColor"
                  d="M512 64a448 448 0 1 1 0 896.064A448 448 0 0 1 512 64zm67.2 275.072c33.28 0 60.288-23.104 60.288-57.344s-27.072-57.344-60.288-57.344c-33.28 0-60.16 23.104-60.16 57.344s26.88 57.344 60.16 57.344zM590.912 699.2c0-6.848 2.368-24.64 1.024-34.752l-52.608 60.544c-10.88 11.456-24.512 19.392-30.912 17.28a12.992 12.992 0 0 1-8.256-14.72l87.68-276.992c7.168-35.136-12.544-67.2-54.336-71.296c-44.096 0-108.992 44.736-148.48 101.504c0 6.784-1.28 23.68.064 33.792l52.544-60.608c10.88-11.328 23.552-19.328 29.952-17.152a12.8 12.8 0 0 1 7.808 16.128L388.48 728.576c-10.048 32.256 8.96 63.872 55.04 71.04c67.84 0 107.904-43.648 147.456-100.416z" />
              </svg>
            </div>
            <span>
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                <path fill="currentColor"
                  d="M6.102 16.98c-1.074 0-1.648-1.264-.94-2.073l5.521-6.31a1.75 1.75 0 0 1 2.634 0l5.522 6.31c.707.809.133 2.073-.94 2.073H6.101Z" />
              </svg>
            </span>
          </h2>
          <div v-for="(item, index) in form.body.parameters" :key="index" class="mt-2 flex items-center space-x-4">
            <div class="w-[30%]">
              <span v-text="'{{' + (index + 1) + '}}'"></span>
            </div>
            <div class="w-full">
              <FormSelect v-model="form.body.parameters[index].selection" :options="variableOptions"
                :class="'sm:col-span-6'" />
            </div>
            <div class="w-full">
              <FormInput v-if="form.body.parameters[index].selection === 'static'"
                v-model="form.body.parameters[index].value" :required="true"
                :error="form.errors['body.parameters.0.value']" :type="'text'" :class="'sm:col-span-6'" />
              <FormSelect v-if="form.body.parameters[index].selection === 'dynamic'"
                v-model="form.body.parameters[index].value" :required="true"
                :error="form.errors['body.parameters.0.value']" :options="dynamicOptions" :class="'sm:col-span-6'" />
            </div>
          </div>
        </div>
        <div v-if="form.carousel.cards.length > 0" class="bg-slate-100 p-3 mt-1 text-sm">
          <h2 class="flex items-center justify-between space-x-2 pb-2 border-b">
            <div class="flex items-center space-x-2">
              <span>{{ $t("Carousel Cards") }}</span>
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 1024 1024">
                <path fill="currentColor"
                  d="M512 64a448 448 0 1 1 0 896.064A448 448 0 0 1 512 64zm67.2 275.072c33.28 0 60.288-23.104 60.288-57.344s-27.072-57.344-60.288-57.344c-33.28 0-60.16 23.104-60.16 57.344s26.88 57.344 60.16 57.344zM590.912 699.2c0-6.848 2.368-24.64 1.024-34.752l-52.608 60.544c-10.88 11.456-24.512 19.392-30.912 17.28a12.992 12.992 0 0 1-8.256-14.72l87.68-276.992c7.168-35.136-12.544-67.2-54.336-71.296c-44.096 0-108.992 44.736-148.48 101.504c0 6.784-1.28 23.68.064 33.792l52.544-60.608c10.88-11.328 23.552-19.328 29.952-17.152a12.8 12.8 0 0 1 7.808 16.128L388.48 728.576c-10.048 32.256 8.96 63.872 55.04 71.04c67.84 0 107.904-43.648 147.456-100.416z" />
              </svg>
            </div>
            <span>
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                <path fill="currentColor"
                  d="M6.102 16.98c-1.074 0-1.648-1.264-.94-2.073l5.521-6.31a1.75 1.75 0 0 1 2.634 0l5.522 6.31c.707.809.133 2.073-.94 2.073H6.101Z" />
              </svg>
            </span>
          </h2>
          <div v-for="(card, cardIndex) in form.carousel.cards" :key="cardIndex" class="mt-4 bg-slate-50 p-3">
            <h3 class="font-medium">{{ $t("Card") }} {{ cardIndex + 1 }}</h3>
            <div v-if="card.header.parameters.length > 0" class="mt-2">
              <h4>{{ $t("Header Image") }}</h4>
              <div class="flex items-center space-x-4">
                <label
                  class="cursor-pointer flex justify-center px-2 py-2 w-[30%] bg-slate-200 shadow-sm rounded-lg border"
                  :class="form.errors[
                    `carousel.cards.${cardIndex}.header.parameters.0.value`
                  ]
                    ? 'border border-red-700'
                    : ''
                    " :for="`file-upload-card-${cardIndex}`">
                  {{ $t("Upload") }}
                </label>
                <input type="file" class="sr-only" :accept="getFileAcceptAttribute(card.header.parameters[0].type)
                  " :id="`file-upload-card-${cardIndex}`" @change="(event) => handleFileUpload(event, cardIndex)" />
                <div v-if="card.header.parameters[0].fileName" class="w-[20em] truncate">
                  {{ card.header.parameters[0].fileName }}
                </div>
                <span v-else>{{ $t("No file chosen") }}</span>
              </div>
              <p v-if="card.header.parameters[0].type === 'IMAGE'" class="text-left text-xs mt-2">
                {{ $t("Max file upload size is") }} <b>5MB</b> <br />
                {{ $t("Supported file extensions") }}: .png, .jpg
              </p>
              <div v-if="
                form.errors[
                `carousel.cards.${cardIndex}.header.parameters.0.value`
                ]
              " class="form-error text-[#b91c1c] text-xs">
                {{
                  form.errors[
                  `carousel.cards.${cardIndex}.header.parameters.0.value`
                  ]
                }}
              </div>
            </div>
            <div v-if="card.body.parameters.length > 0" class="mt-2">
              <h4>{{ $t("Body Variables") }}</h4>
              <div v-for="(param, paramIndex) in card.body.parameters" :key="paramIndex"
                class="mt-2 flex items-center space-x-4">
                <div class="w-[30%]">
                  <span v-text="'{{' + (paramIndex + 1) + '}}'"></span>
                </div>
                <div class="w-full">
                  <FormSelect v-model="param.selection" :options="variableOptions" :class="'sm:col-span-6'" />
                </div>
                <div class="w-full">
                  <FormInput v-if="param.selection === 'static'" v-model="param.value" :required="true" :error="form.errors[
                    `carousel.cards.${cardIndex}.body.parameters.${paramIndex}.value`
                  ]
                    " :type="'text'" :class="'sm:col-span-6'" />
                  <FormSelect v-if="param.selection === 'dynamic'" v-model="param.value" :required="true" :error="form.errors[
                    `carousel.cards.${cardIndex}.body.parameters.${paramIndex}.value`
                  ]
                    " :options="dynamicOptions" :class="'sm:col-span-6'" />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-if="form.buttons.length > 0" class="bg-slate-100 p-3 mt-1 text-sm">
          <h2 class="flex items-center justify-between space-x-2 pb-2 border-b">
            <div class="flex items-center space-x-2">
              <span>{{ $t("Button variables") }}</span>
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 1024 1024">
                <path fill="currentColor"
                  d="M512 64a448 448 0 1 1 0 896.064A448 448 0 0 1 512 64zm67.2 275.072c33.28 0 60.288-23.104 60.288-57.344s-27.072-57.344-60.288-57.344c-33.28 0-60.16 23.104-60.16 57.344s26.88 57.344 60.16 57.344zM590.912 699.2c0-6.848 2.368-24.64 1.024-34.752l-52.608 60.544c-10.88 11.456-24.512 19.392-30.912 17.28a12.992 12.992 0 0 1-8.256-14.72l87.68-276.992c7.168-35.136-12.544-67.2-54.336-71.296c-44.096 0-108.992 44.736-148.48 101.504c0 6.784-1.28 23.68.064 33.792l52.544-60.608c10.88-11.328 23.552-19.328 29.952-17.152a12.8 12.8 0 0 1 7.808 16.128L388.48 728.576c-10.048 32.256 8.96 63.872 55.04 71.04c67.84 0 107.904-43.648 147.456-100.416z" />
              </svg>
            </div>
            <span>
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                <path fill="currentColor"
                  d="M6.102 16.98c-1.074 0-1.648-1.264-.94-2.073l5.521-6.31a1.75 1.75 0 0 1 2.634 0l5.522 6.31c.707.809.133 2.073-.94 2.073H6.101Z" />
              </svg>
            </span>
          </h2>
          <div v-for="(item, index) in form.buttons" :key="index">
            <div v-if="item.parameters.length > 0" class="mt-4 bg-slate-50 p-3">
              <div class="w-[100%] mb-1">
                <span>{{ $t("Label") }}: {{ item.text }}</span>
              </div>
              <div v-for="(value, key) in item.parameters" :key="key" class="flex items-center space-x-4">
                <div class="w-full">
                  <FormSelect v-model="value.type" :name="$t('Button type')" :options="variableOptions"
                    :class="'sm:col-span-6'" />
                </div>
                <div class="w-full">
                  <FormInput v-if="value.type === 'static'" v-model="value.value" :name="$t('Value')" :required="true"
                    :error="form.errors['buttons.' + index + '.parameters.0.value']
                      " :type="'text'" :class="'sm:col-span-6'" />
                  <FormSelect v-if="value.type === 'dynamic'" v-model="value.value" :name="$t('Value')" :required="true"
                    :error="form.errors['buttons.' + index + '.parameters.0.value']
                      " :options="dynamicOptions" :class="'sm:col-span-6'" />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="flex items-center justify-end space-x-3 mt-3">
          <div v-if="displayCancelBtn">
            <Link href="/campaigns"
              class="block rounded-md px-3 py-2 text-sm text-gray-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 bg-slate-200">
            {{ $t("Cancel") }}
            </Link>
          </div>
          <div>
            <button type="submit"
              class="rounded-md px-3 py-2 text-sm text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 bg-primary"
              :disabled="isLoading">
              <span v-if="!isLoading">{{
                sendText ? sendText : $t("Save")
              }}</span>
              <svg v-else xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path fill="currentColor"
                  d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z" opacity=".5" />
                <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                  <animateTransform attributeName="transform" dur="1s" from="0 12 12" repeatCount="indefinite"
                    to="360 12 12" type="rotate" />
                </path>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </form>
    <div class="md:w-[50%] py-20 flex justify-center chat-bg" :class="isCampaignFlow ? 'px-20' : 'px-10'">
      <div>
        <WhatsappTemplate :parameters="form" :visible="form.template ? true : false" />

        <CarouselPreview v-if="form.carousel.cards.length > 0" :cards="form.carousel.cards" />
      </div>
    </div>
  </div>
</template> -->


<!-- ===================================== NEW UI CODE ===================================== -->

<script setup>
import axios from "axios";
import FormInput from "@/Components/FormInput.vue";
import FormSelect from "@/Components/FormSelect.vue";
import WhatsappTemplate from "@/Components/WhatsappTemplate.vue";
import { ref, onMounted } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import "vue3-toastify/dist/index.css";
import { trans } from "laravel-vue-i18n";
import { router } from "@inertiajs/vue3";
import {
  MessageSquare, Send, Clock, FileText, Image as ImageIcon,
  Video, FileIcon, ChevronDown, ChevronUp, Sparkles, Zap, X,
  Layers, Upload, Loader2, Info
} from 'lucide-vue-next';

const props = defineProps({
  templates: Object,
  contactGroups: Object,
  settings: Array,
  contact: {
    type: String,
    default: null,
  },
  displayTitle: {
    type: Boolean,
    default: false,
  },
  displayCancelBtn: {
    type: Boolean,
    default: true,
  },
  isCampaignFlow: {
    type: Boolean,
    default: true,
  },
  sendText: {
    type: String,
    default: "Save",
  },
  form: Object,
  settingsParsed: Object,
  isLoading: Boolean,
  contactGroupOptions: Array,
  templateOptions: Array,
  variableOptions: Array,
  dynamicOptions: Array,
});

const isTempLoading = ref(false);
const isLoading = ref(false);
const expandedSections = ref({
  header: true,
  body: true,
  carousel: true,
  buttons: true
});

const contactGroupOptions = ref([
  { value: "all", label: trans("all contacts") },
]);
const templateOptions = ref([]);
const config = ref(props.settings?.metadata);

const variableOptions = ref([
  { value: "static", label: trans("static") },
  { value: "dynamic", label: trans("dynamic") },
]);

const dynamicOptions = ref([
  { value: "first name", label: trans("contact first name") },
  { value: "last name", label: trans("contact last name") },
  { value: "name", label: trans("contact full name") },
  { value: "phone", label: trans("contact phone") },
  { value: "email", label: trans("contact email") },
]);

const form = useForm({
  name: null,
  template: null,
  contacts: null,
  time: null,
  skip_schedule: false,
  header: {
    format: null,
    text: null,
    parameters: [],
  },
  body: {
    text: null,
    parameters: [],
  },
  footer: {
    text: null,
  },
  buttons: [],
  carousel: {
    cards: [],
  },
});

const toggleSection = (section) => {
  expandedSections.value[section] = !expandedSections.value[section];
};

const loadTemplate = async () => {
  try {
    const response = await axios.get("/templates/" + form.template);
    if (response) {
      console.log("Template data fetched successfully:", response.data);
      const metadata = JSON.parse(response.data.metadata);
      console.log("Template Metadata from GET api:", metadata);
      form.header.format = extractComponent(metadata, "HEADER", "format");
      form.header.text = extractComponent(metadata, "HEADER", "text");
      const headerExamples = extractComponent(metadata, "HEADER", "example");
      if (headerExamples) {
        if (form.header.format === "TEXT") {
          form.header.parameters = headerExamples.header_text.map((item) => ({
            type: "text",
            selection: "static",
            value: item,
          }));
        } else if (
          ["IMAGE", "DOCUMENT", "VIDEO"].includes(form.header.format)
        ) {
          form.header.parameters = headerExamples.header_handle.map(() => ({
            type: form.header.format,
            selection: "upload",
            value: null,
            url: null,
            fileName: null,
          }));
        }
      } else {
        form.header.parameters = [];
      }

      form.body.text = extractComponent(metadata, "BODY", "text");
      const bodyExamples = extractComponent(metadata, "BODY", "example");
      if (bodyExamples) {
        form.body.parameters = bodyExamples.body_text[0].map((item) => ({
          type: "text",
          selection: "static",
          value: item,
        }));
      } else {
        form.body.parameters = [];
      }

      form.footer.text = extractComponent(metadata, "FOOTER", "text");

      const buttons = extractComponent(metadata, "BUTTONS", "buttons");
      if (buttons) {
        form.buttons = buttons.map((item) => ({
          type: item.type,
          text: item.text,
          value: item[item.type.toLowerCase()] ?? null,
          parameters:
            item.type === "QUICK_REPLY"
              ? [{ type: "static", value: null }]
              : item.example
                ? item.example.map((param) => ({ type: "static", value: param }))
                : [],
        }));
      } else {
        form.buttons = [];
      }

      const carousel = metadata.components.find((c) => c.type === "CAROUSEL");
      if (carousel) {
        form.carousel.cards = carousel.cards.map((card) => {
          const cardHeader = card.components.find((c) => c.type === "HEADER");
          const cardBody = card.components.find((c) => c.type === "BODY");
          const cardButtons = card.components.find((c) => c.type === "BUTTONS");

          return {
            header: {
              format: cardHeader?.format || null,
              parameters: cardHeader?.example?.header_handle
                ? cardHeader.example.header_handle.map(() => ({
                  type: cardHeader.format,
                  selection: "upload",
                  value: null,
                  url: null,
                  fileName: null,
                }))
                : [],
            },
            body: {
              text: cardBody?.text || null,
              parameters: cardBody?.example?.body_text?.[0]
                ? cardBody.example.body_text[0].map((item) => ({
                  type: "text",
                  selection: "static",
                  value: item,
                }))
                : [],
            },
            buttons: cardButtons?.buttons
              ? cardButtons.buttons.map((button) => ({
                type: button.type,
                text: button.text,
                value: button[button.type.toLowerCase()] ?? null,
                parameters:
                  button.type === "QUICK_REPLY"
                    ? [{ type: "static", value: null }]
                    : button.example
                      ? button.example.map((param) => ({
                        type: "static",
                        value: param,
                      }))
                      : [],
              }))
              : [],
          };
        });
      } else {
        form.carousel.cards = [];
      }
    }
  } catch (error) {
    console.error("Error fetching data:", error);
  }
};

const uploadMedia = async (file, type) => {
  const formData = new FormData();
  formData.append("file", file);
  formData.append("type", type.toLowerCase());
  formData.append("messaging_product", "whatsapp");

  try {
    const response = await axios.post("/upload-file", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });
    console.log("Media uploaded successfully:", response.data);
    return response.data.public_url;
  } catch (error) {
    console.error("Error uploading media:", error);
    throw error;
  }
};

const handleFileUpload = async (event, cardIndex = null) => {
  const fileType =
    cardIndex !== null
      ? form.carousel.cards[cardIndex].header.parameters[0].type
      : form.header.parameters[0].type;

  const fileSizeLimit = getFileSizeLimit(fileType);
  const file = event.target.files[0];
  isTempLoading.value = true;
  if (file && file.size > fileSizeLimit) {
    alert(
      trans("file size exceeds the limit. Max allowed size:") +
      " " +
      fileSizeLimit +
      "b"
    );
    event.target.value = null;
    return;
  }

  try {
    const url = await uploadMedia(file, fileType);
    console.log("File uploaded successfully:", url);
    if (cardIndex !== null) {
      form.carousel.cards[cardIndex].header.parameters[0].url = url;
      form.carousel.cards[cardIndex].header.parameters[0].fileName = file.name;
    } else {
      form.header.parameters[0].url = url;
      form.header.parameters[0].fileName = file.name;
    }
  } catch (error) {
    alert(trans("Failed to upload file"));
    event.target.value = null;
  } finally {
    isTempLoading.value = false;
  }
};

const getFileAcceptAttribute = (fileType) => {
  switch (fileType) {
    case "IMAGE":
      return ".png, .jpg";
    case "DOCUMENT":
      return ".pdf, .txt, .ppt, .doc, .xls, .docx, .pptx, .xlsx";
    case "VIDEO":
      return ".mp4";
    default:
      return "";
  }
};

const getFileSizeLimit = (fileType) => {
  switch (fileType) {
    case "IMAGE":
      return 5 * 1024 * 1024;
    case "DOCUMENT":
      return 100 * 1024 * 1024;
    case "VIDEO":
      return 16 * 1024 * 1024;
    default:
      return Infinity;
  }
};

const extractComponent = (data, type, customProperty) => {
  const component = data.components.find((c) => c.type === type);
  return component ? component[customProperty] : null;
};

const transformOptions = (options) => {
  return options.map((option) => ({
    value: option.uuid,
    label: option.language
      ? option.name + " [" + option.language + "]"
      : option.name,
  }));
};

const submitForm = () => {
  isLoading.value = true;

  const url = props.isCampaignFlow
    ? "/campaigns"
    : `/chat/${props.contact}/send/template`;
  const payload = form.data();
  console.log(props.isCampaignFlow ? "Campaign Flow" : "Single Contact Flow");

  console.log("POST Request:", JSON.stringify({ url, payload }, null, 2));

  form.post(url, {
    onFinish: () => {
      isLoading.value = false;
      if (!props.isCampaignFlow) {
        emit("viewTemplate", false);
      }
    },
  });
};

const submitCarouselForm = async () => {
  isLoading.value = true;

  const payload = {
    messaging_product: "whatsapp",
    recipient_type: "individual",
    to: props.isCampaignFlow ? form.contacts : props.contact,
    type: "template",
    template: {
      name: form.template,
      language: {
        code: "en",
      },
      components: [],
    },
  };

  if (form.carousel.cards.length > 0) {
    const carouselComponent = {
      type: "carousel",
      cards: form.carousel.cards.map((card, index) => ({
        card_index: index,
        components: [
          {
            type: "header",
            parameters: card.header.parameters.map((param) => ({
              type: param.type.toLowerCase(),
              [param.type.toLowerCase()]: {
                link: param.url,
              },
            })),
          },
        ],
      })),
    };
    payload.template.components.push(carouselComponent);
  }

  if (form.header.format && form.header.parameters.length > 0) {
    payload.template.components.push({
      type: "header",
      parameters: form.header.parameters.map((param) => ({
        type: param.type.toLowerCase(),
        [param.type.toLowerCase()]: {
          id: param.url,
        },
      })),
    });
  }

  if (form.body.text) {
    payload.template.components.push({
      type: "body",
      parameters: form.body.parameters.map((param) => ({
        type: "text",
        text: param.selection === "static" ? param.value : `{{${param.value}}}`,
      })),
    });
  }

  if (form.footer.text) {
    payload.template.components.push({
      type: "footer",
      text: form.footer.text,
    });
  }

  if (form.buttons.length > 0) {
    payload.template.components.push({
      type: "buttons",
      buttons: form.buttons.map((button) => ({
        type: button.type,
        text: button.text,
        ...(button.parameters.length > 0
          ? {
            parameters: button.parameters.map((param) => ({
              type: "text",
              text:
                param.type === "static" ? param.value : `{{${param.value}}}`,
            })),
          }
          : {}),
      })),
    });
  }

  try {
    const url = "/send/crousel";

    console.log(
      "POST Request:",
      JSON.stringify(
        {
          url,
          payload,
          campaign_name: form.name,
        },
        null,
        2
      )
    );

    const res = await axios.post(url, {
      payload,
      campaign_name: form.name,
    });
    console.log("Response:", res.data);
    if (!props.isCampaignFlow) {
      emit("viewTemplate", false);
    }
  } catch (error) {
    console.error("Error sending carousel message:", error);
    alert(trans("Failed to send carousel message"));
  } finally {
    isLoading.value = false;
  }
};

const handleSubmit = async () => {
  if (form.carousel.cards.length > 0) {
    await submitCarouselForm();
  } else {
    await submitForm();
  }
  router.visit("/campaigns");
};

const emit = defineEmits(["viewTemplate"]);

const viewTemplate = () => {
  emit("viewTemplate", false);
};

onMounted(() => {
  const filteredTemplates = props.templates.filter(template =>
    template.name?.startsWith('crousel_')
  );

  templateOptions.value = transformOptions(filteredTemplates);

  contactGroupOptions.value = [
    ...contactGroupOptions.value,
    ...transformOptions(props.contactGroups)
  ];
});

</script>


<template>
  <div class="w-full lg:h-screen bg-[#EEEEEE] flex flex-col lg:flex-row overflow-hidden"
    :class="isCampaignFlow ? `lg:max-h-[calc(100vh-170px)]` : `lg:max-h-[calc(100vh-145px)]`">
    <!-- Left Panel - Form -->
    <div class="w-full lg:w-1/2 overflow-y-auto">
      <form @submit.prevent="handleSubmit()" class="p-6 lg:p-8 space-y-6">
        <!-- Header Card -->
        <div v-if="displayTitle" class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <!-- <div
                class="w-10 h-10 bg-gradient-to-br from-[#ff5100] to-[#ff6422] rounded-xl flex items-center justify-center">
                <Layers class="w-5 h-5 text-white" />
              </div> -->
              <h3 class="text-sm font-bold text-slate-800">{{ $t("Send Carousel Message") }}</h3>
            </div>
            <button @click="viewTemplate()" type="button"
              class="px-3 py-1.5 bg-primary hover:bg-primary/90 text-white rounded-lg transition-colors duration-200 flex items-center space-x-2">
              <X class="w-4 h-4" />
              <span class="hidden md:inline">Cancel</span>
            </button>
          </div>
        </div>

        <!-- Campaign Details Card -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 space-y-5">
          <div class="flex items-center space-x-2 mb-4">
            <FileText class="w-5 h-5 text-[#ff5100]" />
            <h4 class="font-semibold text-slate-800">Campaign Details</h4>
          </div>

          <FormInput v-if="isCampaignFlow" v-model="form.name" :name="$t('Campaign name')" :type="'text'"
            :error="form.errors.name" :required="true" :class="'w-full'" />

          <div class="grid grid-cols-1 gap-4">
            <FormSelect v-model="form.template" @update:modelValue="loadTemplate" :options="templateOptions"
              :required="true" :error="form.errors.template" :name="$t('Template')"
              :placeholder="$t('Select template')" />

            <FormSelect v-if="isCampaignFlow" v-model="form.contacts" :options="contactGroupOptions"
              :name="$t('Send to')" :required="true" :placeholder="$t('Select contacts')"
              :error="form.errors.contacts" />
          </div>

          <FormInput v-if="isCampaignFlow && !form.skip_schedule" v-model="form.time" :name="$t('Scheduled time')"
            :type="'datetime-local'" :error="form.errors.time" :required="true" :class="'w-full'" />

          <div v-if="isCampaignFlow"
            class="flex items-center space-x-3 p-4 bg-slate-50 rounded-xl border border-slate-200">
            <input v-model="form.skip_schedule" id="skip-schedule" type="checkbox"
              class="w-5 h-5 rounded border-slate-300 text-[#ff5100] focus:ring-[#ff5100] focus:ring-2 cursor-pointer">
            <label for="skip-schedule"
              class="text-sm font-medium text-slate-700 cursor-pointer flex items-center space-x-2">
              <Clock class="w-4 h-4" />
              <span>{{ $t('Skip scheduling & send immediately') }}</span>
            </label>
          </div>
        </div>

        <!-- Header Variables -->
        <div v-if="form.header.parameters.length > 0"
          class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
          <button type="button" @click="toggleSection('header')"
            class="w-full p-5 flex items-center justify-between hover:bg-slate-50 transition-colors">
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-[#ff5100]/10 rounded-lg flex items-center justify-center">
                <ImageIcon class="w-4 h-4 text-[#ff5100]" />
              </div>
              <h4 class="font-semibold text-slate-800">{{ $t('Header Variables') }}</h4>
              <span class="px-2 py-1 bg-[#ff5100]/10 text-[#ff5100] text-xs font-medium rounded-full">
                {{ form.header.parameters.length }}
              </span>
            </div>
            <component :is="expandedSections.header ? ChevronUp : ChevronDown"
              class="w-5 h-5 text-slate-400 transition-transform" />
          </button>

          <div v-show="expandedSections.header" class="p-5 pt-0 space-y-4 border-t border-slate-100">
            <div v-for="(item, index) in form.header.parameters" :key="index"
              class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">

              <template v-if="item.type === 'text'">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <FormSelect v-model="form.header.parameters[index].selection" :name="$t('Content type')"
                    :options="variableOptions" />

                  <FormInput v-if="form.header.parameters[index].selection === 'static'" :name="$t('Value')"
                    :required="true" :error="form.errors['header.parameters.0.value']"
                    v-model="form.header.parameters[index].value" :type="'text'" />

                  <FormSelect v-if="form.header.parameters[index].selection === 'dynamic'" :name="$t('Value')"
                    :required="true" :error="form.errors['header.parameters.0.value']"
                    v-model="form.header.parameters[index].value" :options="dynamicOptions" />
                </div>
              </template>

              <template v-if="['IMAGE', 'DOCUMENT', 'VIDEO'].includes(item.type)">
                <div class="space-y-3">
                  <div class="flex items-center space-x-3">
                    <label for="file-upload"
                      class="cursor-pointer flex items-center justify-center space-x-2 px-4 py-2 bg-gradient-to-r from-[#ff5100] to-[#ff6422] text-white rounded-lg hover:shadow-lg transition-all duration-200">
                      <component :is="item.type === 'IMAGE' ? ImageIcon : item.type === 'VIDEO' ? Video : FileIcon"
                        class="w-4 h-4" />
                      <span>{{ $t('Upload') }}</span>
                    </label>
                    <input type="file" class="sr-only" :accept="getFileAcceptAttribute(item.type)" id="file-upload"
                      @change="handleFileUpload" />

                    <div v-if="isTempLoading" class="flex items-center space-x-2">
                      <Loader2 class="w-4 h-4 animate-spin text-[#ff5100]" />
                      <span class="text-sm text-slate-600">Uploading...</span>
                    </div>
                    <span v-else-if="item.fileName" class="text-sm text-slate-600 truncate flex-1">
                      {{ item.fileName }}
                    </span>
                    <span v-else class="text-sm text-slate-400">{{ $t('No file chosen') }}</span>
                  </div>
                  <p class="text-xs text-slate-500 flex items-start space-x-2">
                    <Info class="w-3 h-3 text-[#ff5100] mt-0.5 flex-shrink-0" />
                    <span v-if="item.type === 'IMAGE'">
                      {{ $t('Max file upload size is') }} <b>5MB</b> | Supported: .png, .jpg
                    </span>
                    <span v-if="item.type === 'DOCUMENT'">
                      {{ $t('Max file upload size is') }} <b>100MB</b> | Supported: .pdf, .txt, .ppt, .doc, .xls,
                      .docx, .pptx, .xlsx
                    </span>
                    <span v-if="item.type === 'VIDEO'">
                      {{ $t('Max file upload size is') }} <b>16MB</b> | Supported: .mp4
                    </span>
                  </p>
                  <div v-if="form.errors['header.parameters.0.value']"
                    class="text-red-600 text-xs flex items-center space-x-1">
                    <X class="w-3 h-3" />
                    <span>{{ form.errors['header.parameters.0.value'] }}</span>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </div>

        <!-- Body Variables -->
        <div v-if="form.body.parameters.length > 0"
          class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
          <button type="button" @click="toggleSection('body')"
            class="w-full p-5 flex items-center justify-between hover:bg-slate-50 transition-colors">
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-[#ff5100]/10 rounded-lg flex items-center justify-center">
                <FileText class="w-4 h-4 text-[#ff5100]" />
              </div>
              <h4 class="font-semibold text-slate-800">{{ $t('Body Variables') }}</h4>
              <span class="px-2 py-1 bg-[#ff5100]/10 text-[#ff5100] text-xs font-medium rounded-full">
                {{ form.body.parameters.length }}
              </span>
            </div>
            <component :is="expandedSections.body ? ChevronUp : ChevronDown"
              class="w-5 h-5 text-slate-400 transition-transform" />
          </button>

          <div v-show="expandedSections.body" class="p-5 pt-0 space-y-3 border-t border-slate-100">
            <div v-for="(item, index) in form.body.parameters" :key="index"
              class="p-4 bg-slate-50 rounded-xl border border-slate-200">
              <div class="grid grid-cols-12 gap-3 items-end">
                <div class="col-span-2 flex items-center justify-center">
                  <span
                    class="px-3 py-1 bg-white border border-slate-300 rounded-lg font-mono text-sm font-semibold text-slate-700"
                    v-text="'{{' + (index + 1) + '}}'"></span>
                </div>
                <div class="col-span-5">
                  <FormSelect v-model="form.body.parameters[index].selection" :options="variableOptions" />
                </div>
                <div class="col-span-5">
                  <FormInput v-if="form.body.parameters[index].selection === 'static'"
                    v-model="form.body.parameters[index].value" :required="true"
                    :error="form.errors['body.parameters.0.value']" :type="'text'" />
                  <FormSelect v-if="form.body.parameters[index].selection === 'dynamic'"
                    v-model="form.body.parameters[index].value" :required="true"
                    :error="form.errors['body.parameters.0.value']" :options="dynamicOptions" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Carousel Cards -->
        <div v-if="form.carousel.cards.length > 0"
          class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
          <button type="button" @click="toggleSection('carousel')"
            class="w-full p-5 flex items-center justify-between hover:bg-slate-50 transition-colors">
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-[#ff5100]/10 rounded-lg flex items-center justify-center">
                <Layers class="w-4 h-4 text-[#ff5100]" />
              </div>
              <h4 class="font-semibold text-slate-800">{{ $t('Carousel Cards') }}</h4>
              <span class="px-2 py-1 bg-[#ff5100]/10 text-[#ff5100] text-xs font-medium rounded-full">
                {{ form.carousel.cards.length }}
              </span>
            </div>
            <component :is="expandedSections.carousel ? ChevronUp : ChevronDown"
              class="w-5 h-5 text-slate-400 transition-transform" />
          </button>

          <div v-show="expandedSections.carousel" class="p-5 pt-0 space-y-4 border-t border-slate-100">
            <div v-for="(card, cardIndex) in form.carousel.cards" :key="cardIndex"
              class="p-5 bg-gradient-to-br from-slate-50 to-orange-50/20 rounded-xl border-2 border-slate-200 space-y-4">

              <!-- Card Header -->
              <div class="flex items-center justify-between pb-3 border-b border-slate-300">
                <div class="flex items-center space-x-2">
                  <div
                    class="w-8 h-8 bg-gradient-to-br from-[#ff5100] to-[#ff6422] rounded-lg flex items-center justify-center text-white font-bold text-sm">
                    {{ cardIndex + 1 }}
                  </div>
                  <span class="font-semibold text-slate-800">{{ $t("Card") }} {{ cardIndex + 1 }}</span>
                </div>
                <Sparkles class="w-4 h-4 text-[#ff5100]" />
              </div>

              <!-- Header Image Upload -->
              <div v-if="card.header.parameters.length > 0" class="space-y-3">
                <h4 class="text-sm font-medium text-slate-700 flex items-center space-x-2">
                  <ImageIcon class="w-4 h-4 text-[#ff5100]" />
                  <span>{{ $t("Header Image") }}</span>
                </h4>
                <div class="flex items-center space-x-3">
                  <label :for="`file-upload-card-${cardIndex}`"
                    class="cursor-pointer flex items-center justify-center space-x-2 px-4 py-2 bg-gradient-to-r from-[#ff5100] to-[#ff6422] text-white rounded-lg hover:shadow-lg transition-all duration-200">
                    <Upload class="w-4 h-4" />
                    <span>{{ $t('Upload') }}</span>
                  </label>
                  <input type="file" class="sr-only" :accept="getFileAcceptAttribute(card.header.parameters[0].type)"
                    :id="`file-upload-card-${cardIndex}`" @change="(event) => handleFileUpload(event, cardIndex)" />

                  <div v-if="isTempLoading" class="flex items-center space-x-2">
                    <Loader2 class="w-4 h-4 animate-spin text-[#ff5100]" />
                    <span class="text-sm text-slate-600">Uploading...</span>
                  </div>
                  <span v-else-if="card.header.parameters[0].fileName" class="text-sm text-slate-600 truncate flex-1">
                    {{ card.header.parameters[0].fileName }}
                  </span>
                  <span v-else class="text-sm text-slate-400">{{ $t('No file chosen') }}</span>
                </div>
                <p v-if="card.header.parameters[0].type === 'IMAGE'"
                  class="text-xs text-slate-500 flex items-start space-x-2">
                  <Info class="w-3 h-3 text-[#ff5100] mt-0.5 flex-shrink-0" />
                  <span>{{ $t('Max file upload size is') }} <b>5MB</b> | Supported: .png, .jpg</span>
                </p>
                <div v-if="form.errors[`carousel.cards.${cardIndex}.header.parameters.0.value`]"
                  class="text-red-600 text-xs flex items-center space-x-1">
                  <X class="w-3 h-3" />
                  <span>{{ form.errors[`carousel.cards.${cardIndex}.header.parameters.0.value`] }}</span>
                </div>
              </div>

              <!-- Body Variables -->
              <div v-if="card.body.parameters.length > 0" class="space-y-3">
                <h4 class="text-sm font-medium text-slate-700 flex items-center space-x-2">
                  <FileText class="w-4 h-4 text-[#ff5100]" />
                  <span>{{ $t("Body Variables") }}</span>
                </h4>
                <div v-for="(param, paramIndex) in card.body.parameters" :key="paramIndex"
                  class="p-3 bg-white rounded-lg border border-slate-200">
                  <div class="grid grid-cols-12 gap-3 items-end">
                    <div class="col-span-2 flex items-center justify-center">

                      <span
                        class="px-2 py-1 bg-slate-100 border border-slate-300 rounded-lg font-mono text-xs font-semibold text-slate-700"
                        v-text="'{{' + (paramIndex + 1) + '}}'"></span>
                    </div>
                    <div class="col-span-5">
                      <FormSelect v-model="param.selection" :options="variableOptions" />
                    </div>
                    <div class="col-span-5">
                      <FormInput v-if="param.selection === 'static'" v-model="param.value" :required="true"
                        :error="form.errors[`carousel.cards.${cardIndex}.body.parameters.${paramIndex}.value`]"
                        :type="'text'" />
                      <FormSelect v-if="param.selection === 'dynamic'" v-model="param.value" :required="true"
                        :error="form.errors[`carousel.cards.${cardIndex}.body.parameters.${paramIndex}.value`]"
                        :options="dynamicOptions" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Button Variables -->
        <div v-if="form.buttons.length > 0"
          class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
          <button type="button" @click="toggleSection('buttons')"
            class="w-full p-5 flex items-center justify-between hover:bg-slate-50 transition-colors">
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-[#ff5100]/10 rounded-lg flex items-center justify-center">
                <Zap class="w-4 h-4 text-[#ff5100]" />
              </div>
              <h4 class="font-semibold text-slate-800">{{ $t('Button Variables') }}</h4>
              <span class="px-2 py-1 bg-[#ff5100]/10 text-[#ff5100] text-xs font-medium rounded-full">
                {{ form.buttons.length }}
              </span>
            </div>
            <component :is="expandedSections.buttons ? ChevronUp : ChevronDown"
              class="w-5 h-5 text-slate-400 transition-transform" />
          </button>

          <div v-show="expandedSections.buttons" class="p-5 pt-0 space-y-4 border-t border-slate-100">
            <div v-for="(item, index) in form.buttons" :key="index">
              <div v-if="item.parameters.length > 0"
                class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                <div class="flex items-center space-x-2 pb-2 border-b border-slate-200">
                  <Zap class="w-4 h-4 text-[#ff5100]" />
                  <span class="text-sm font-medium text-slate-700">
                    {{ $t('Label') }}: {{ item.text }}
                  </span>
                </div>
                <div v-for="(value, key) in item.parameters" :key="key" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <FormSelect v-model="value.type" :name="$t('Button type')" :options="variableOptions" />

                  <FormInput v-if="value.type === 'static'" v-model="value.value" :name="$t('Value')" :required="true"
                    :error="form.errors['buttons.' + index + '.parameters.0.value']" :type="'text'" />

                  <FormSelect v-if="value.type === 'dynamic'" v-model="value.value" :name="$t('Value')" :required="true"
                    :error="form.errors['buttons.' + index + '.parameters.0.value']" :options="dynamicOptions" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-3 sticky bottom-0 bg-white p-6 rounded-2xl">
          <Link v-if="displayCancelBtn" href="/campaigns"
            class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-all duration-200 border border-slate-300">
          {{ $t('Cancel') }}
          </Link>

          <button type="submit"
            class="px-6 py-3 bg-gradient-to-r from-[#ff5100] to-[#ff6422] hover:from-[#ff6422] hover:to-[#ff5100] text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
            :disabled="isLoading">
            <template v-if="!isLoading">
              <Send class="w-4 h-4" />
              <span>{{ sendText ? sendText : $t('Save') }}</span>
            </template>
            <template v-else>
              <Loader2 class="w-5 h-5 animate-spin" />
              <span>{{ $t('Sending...') }}</span>
            </template>
          </button>
        </div>
      </form>
    </div>

    <!-- Right Panel - Preview -->
    <div
      class="w-full lg:w-1/2 bg-gradient-to-br from-slate-100 via-orange-50/20 to-slate-100 p-4 flex items-center justify-center relative">
      <!-- Background Decorations -->
      <div v-if="isCampaignFlow"
        class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-[#ff5100]/5 to-transparent rounded-full blur-3xl">
      </div>
      <div v-if="isCampaignFlow"
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
              <WhatsappTemplate :parameters="form" :visible="form.template ? true : false" />

              <CarouselPreview v-if="form.carousel.cards.length > 0" :cards="form.carousel.cards" />
              <!-- Empty State -->
              <div v-if="!form.template" class="flex flex-col items-center justify-center h-full text-center px-6">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-4">
                  <MessageSquare class="w-10 h-10 text-green-500" />
                </div>
                <h4 class="font-semibold text-green-600 mb-2">No Template Selected</h4>
                <p class="text-sm text-black">Select a template to preview your message
                </p>
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
</template>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
  }

  to {
    opacity: 1;
  }
}

@keyframes zoom-in {
  from {
    transform: scale(0.95);
  }

  to {
    transform: scale(1);
  }
}

.animate-in {
  animation-duration: 0.3s;
  animation-timing-function: ease-out;
  animation-fill-mode: both;
}

.fade-in {
  animation-name: fade-in;
}

.zoom-in {
  animation-name: zoom-in;
}


/* Smooth transitions */
* {
  transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

button,
a {
  transition-duration: 200ms;
}

/* Loading spinner animation */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Pulse animation for live indicator */
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

/* Hover effects for cards */
.hover\:scale-\[1\.02\]:hover {
  transform: scale(1.02);
}

/* Focus states */
input:focus,
select:focus,
textarea:focus {
  outline: 2px solid #ff5100;
  outline-offset: 2px;
}

/* Checkbox custom styling */
input[type="checkbox"]:checked {
  background-color: #ff5100;
  border-color: #ff5100;
}

/* Disabled state */
button:disabled {
  cursor: not-allowed;
  opacity: 0.5;
}

/* Gradient text effect */
.gradient-text {
  background: linear-gradient(135deg, #ff5100, #ff6422);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Card hover effects */
.bg-white:hover {
  box-shadow: 0 10px 25px -5px rgba(255, 81, 0, 0.1), 0 8px 10px -6px rgba(255, 81, 0, 0.1);
}

/* Carousel card special styling */
.bg-gradient-to-br.from-slate-50 {
  position: relative;
  overflow: hidden;
}

.bg-gradient-to-br.from-slate-50::before {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 100px;
  height: 100px;
  background: radial-gradient(circle, rgba(255, 81, 0, 0.05) 0%, transparent 70%);
  border-radius: 50%;
}

/* Mobile responsiveness improvements */
@media (max-width: 768px) {
  .grid-cols-12 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }

  .col-span-2,
  .col-span-5 {
    grid-column: span 12 / span 12;
  }
}

/* Card numbering badge animation */
.w-8.h-8.bg-gradient-to-br {
  animation: badge-pop 0.3s ease-out;
}

@keyframes badge-pop {
  0% {
    transform: scale(0.8);
  }

  50% {
    transform: scale(1.1);
  }

  100% {
    transform: scale(1);
  }
}
</style>