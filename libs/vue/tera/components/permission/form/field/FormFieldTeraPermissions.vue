<template>
  <FormField
    :validate-on-blur="!isFieldDirty"
    name="permissions"
    :rules="fieldSchema"
  >
    <FormItem class="flex flex-col">
      <FormLabel>{{ tLandRole('property.permissions.label') }}</FormLabel>
      <Combobox by="label">
        <FormControl class="w-full">
          <ComboboxAnchor>
            <TagsInput
              v-model="selectedPermissions"
              class="px-2 gap-2"
              @update:model-value="emit('update:modelValue', selectedPermissions)"
            >
              <div class="flex gap-2 flex-wrap items-center">
                <TagsInputItem
                  v-for="item in selectedPermissions"
                  :key="item"
                  :value="item"
                >
                  <TagsInputItemText class="px-2">
                    {{ t(item) }}
                  </TagsInputItemText>
                  <TagsInputItemDelete @delete="deletePermission(item)" />
                </TagsInputItem>
              </div>

              <ComboboxInput
                v-model="searchTerm"
                as-child
              >
                <TagsInputInput
                  placeholder="Permissions..."
                  class="min-w-[200px] w-full p-0 border-none bg-transparent shadow-none focus-visible:ring-0 h-auto"
                  @keydown.enter.prevent
                />
              </ComboboxInput>
              <Button
                size="xs"
                @click.stop.prevent="addAllOptions()"
              >
                <template #icon> <IconShieldCheck /> </template
              ></Button>
            </TagsInput>
          </ComboboxAnchor>
        </FormControl>

        <ComboboxList>
          <ComboboxEmpty> Nothing found. </ComboboxEmpty>

          <ComboboxGroup>
            <ComboboxItem
              v-for="permission in filteredPermissions"
              :key="permission.value"
              :value="permission.value"
              @select.prevent="handlePermissionSelect(permission.value)"
            >
              {{ permission.label }}

              <ComboboxItemIndicator>
                <IconCheck class="ml-auto h-4 w-4" />
              </ComboboxItemIndicator>
            </ComboboxItem>
          </ComboboxGroup>
        </ComboboxList>
      </Combobox>

      <FormDescription> Select permissions for this role. </FormDescription>
      <FormMessage />
    </FormItem>
  </FormField>
</template>

<script setup lang="ts">
import {
  Combobox,
  ComboboxAnchor,
  ComboboxEmpty,
  ComboboxGroup,
  ComboboxInput,
  ComboboxItem,
  ComboboxItemIndicator,
  ComboboxList,
} from '@lychen/vue-components-core/combobox';

import {
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@lychen/vue-components-core/form';
import { useFilter } from 'reka-ui';

import { toTypedSchema } from '@vee-validate/zod';
import { computed, ref, onMounted, watch } from 'vue';
import * as z from 'zod';
import {
  TagsInputItem,
  TagsInputItemDelete,
  TagsInput,
  TagsInputInput,
} from '@lychen/vue-components-core/tags-input';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { messages, TRANSLATION_KEY } from '@lychen/i18n-tera/permission';
import {
  messages as landRoleMessages,
  TRANSLATION_KEY as LAND_ROLE_TRANSLATION_KEY,
} from '@lychen/i18n-tera/land-role';
import Button from '@lychen/vue-components-core/button/Button.vue';

import { LandRoleLand_rolePatch_land_rolePatchInputPermissions as LandRolePermissions } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import IconCheck from '@lychen/vue-icons/IconCheck.vue';
import IconShieldCheck from '@lychen/vue-icons/IconShieldCheck.vue';

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });
const { t: tLandRole } = useI18nExtended({
  messages: landRoleMessages,
  rootKey: LAND_ROLE_TRANSLATION_KEY,
  prefixed: true,
});
const allPermissions = Object.values(LandRolePermissions);

const permissionOptions = ref<{ label: string; value: string }[]>([]);
const selectedPermissions = ref<string[]>([]);

const props = defineProps({
  isFieldDirty: {
    type: Boolean,
    required: true,
  },
  modelValue: {
    type: Array as () => string[],
    required: false,
    default: () => [],
  },
});

const emit = defineEmits(['update:modelValue']);

watch(
  () => props.modelValue,
  (newVal) => {
    selectedPermissions.value = [...newVal];
  },
  { immediate: true, deep: true },
);

onMounted(async () => {
  permissionOptions.value = await Promise.all(
    allPermissions.map(async (permission) => ({
      value: permission,
      label: await t(permission),
    })),
  );
});

const fieldSchema = toTypedSchema(
  z
    .array(z.nativeEnum(LandRolePermissions), {
      required_error: 'Please select at least one permission.',
    })
    .min(1),
);

const searchTerm = ref('');

const { contains } = useFilter({ sensitivity: 'base' });
const filteredPermissions = computed(() => {
  return searchTerm.value
    ? permissionOptions.value.filter(
        (option) =>
          contains(option.label, searchTerm.value) &&
          !selectedPermissions.value.includes(option.value),
      )
    : permissionOptions.value.filter((option) => !selectedPermissions.value.includes(option.value));
});

function handlePermissionSelect(value: string) {
  if (!selectedPermissions.value.includes(value)) {
    selectedPermissions.value.push(value);
    emit('update:modelValue', selectedPermissions.value);
  }
  searchTerm.value = ''; // Clear search term after selection
}

function addAllOptions() {
  selectedPermissions.value = permissionOptions.value.map((item) => item.value);
  emit('update:modelValue', selectedPermissions.value);
}

function deletePermission(permission: string) {
  selectedPermissions.value = selectedPermissions.value.filter((p) => p !== permission);
  emit('update:modelValue', selectedPermissions.value);
}
</script>
