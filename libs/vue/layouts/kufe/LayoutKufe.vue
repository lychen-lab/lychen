<template>
  <LayoutKufeRoot>
    <LayoutKufeHeader><slot name="header" /></LayoutKufeHeader>
    <LayoutKufeNav>
      <slot name="nav" />
      <LayoutKufeSheetMobile class="lg:hidden">
        <template #trigger>
          <Button
            icon-only
            variant="ghost"
          >
            <template #icon>
              <IconMenu />
            </template>
          </Button>
        </template>
        <template #content>
          <slot name="sheetMobileContent" />
        </template>
      </LayoutKufeSheetMobile>
    </LayoutKufeNav>
    <LayoutKufeMain>
      <slot name="main" />
    </LayoutKufeMain>
    <LayoutKufeFooter>
      <slot name="footer" />
    </LayoutKufeFooter>
  </LayoutKufeRoot>
</template>

<script setup lang="ts">
import IconMenu from '@lychen/vue-icons/IconMenu.vue';
import {
  LayoutKufeFooter,
  LayoutKufeHeader,
  LayoutKufeMain,
  LayoutKufeNav,
  LayoutKufeRoot,
} from '.';
import LayoutKufeSheetMobile from './LayoutKufeSheetMobile.vue';
import Button from '@lychen/vue-components-core/button/Button.vue';
</script>
