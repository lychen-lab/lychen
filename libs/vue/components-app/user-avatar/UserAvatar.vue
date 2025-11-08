<template>
  <Popover>
    <PopoverTrigger as-child>
      <Avatar class="cursor-pointer">
        <AvatarFallback> {{ avatarTag }} </AvatarFallback>
      </Avatar>
    </PopoverTrigger>
    <PopoverContent class="flex flex-col items-center gap-4 p-8">
      <div class="flex flex-col items-center gap-0">
        <small>{{ email }}</small>
      </div>
      <div class="flex flex-col items-center gap-2">
        <Avatar
          class="cursor-pointer"
          size="lg"
        >
          <AvatarFallback> {{ avatarTag }} </AvatarFallback>
        </Avatar>
        <h2 class="text-lg font-medium">{{ firstName }} {{ lastName }}</h2>
        <Button
          label="Gérez votre compte"
          variant="outline"
          size="sm"
        />
      </div>
      <div class="flex flex-row items-center justify-between self-stretch text-xs">
        <a class="opacity-90"><IconRightFromBracket class="mr-2" /> Se déconnecter </a>
        <div class="flex flex-row gap-2">
          <!--<SelectLanguage />-->
          <ToggleColorScheme />
        </div>
      </div>
    </PopoverContent>
  </Popover>
</template>

<script setup lang="ts">
import { Avatar, AvatarFallback } from '@lychen/vue-components-core/avatar';
import { Popover, PopoverContent, PopoverTrigger } from '@lychen/vue-components-core/popover';
import ToggleColorScheme from '@lychen/vue-color-scheme/components/ToggleColorScheme.vue';
import Button from '@lychen/vue-components-core/button/Button.vue';
import IconRightFromBracket from '@lychen/vue-icons/IconRightFromBracket.vue';
import { computed } from 'vue';

const props = defineProps<{
  firstName: string | undefined;
  lastName: string | undefined;
  email: string | undefined;
}>();

const avatarTag = computed(() => {
  const givenName = props.firstName;
  const familyName = props.lastName;
  if (givenName && familyName) {
    return `${givenName.charAt(0)}${familyName.charAt(0)}`;
  }
  if (givenName) {
    return givenName.charAt(0);
  }
  if (familyName) {
    return familyName.charAt(0);
  }
  return 'AA';
});
</script>
