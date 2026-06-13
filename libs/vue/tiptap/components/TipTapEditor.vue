<template>
  <div
    v-if="editor"
    class="col"
  >
    <BubbleMenu
      v-if="!noStyling"
      :editor="editor"
      :tippy-options="{ duration: 100 }"
      class="bubble-menu"
    >
      <button
        :class="{ 'is-active': editor.isActive('heading', { level: 1 }) }"
        @click="editor.chain().focus().toggleHeading({ level: 1 }).run()"
      >
        <FaIcon name="h1" />
      </button>
      <button
        :class="{ 'is-active': editor.isActive('heading', { level: 2 }) }"
        @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
      >
        <FaIcon name="h2" />
      </button>
      <button
        :class="{ 'is-active': editor.isActive('heading', { level: 3 }) }"
        @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
      >
        <FaIcon name="h3" />
      </button>
      <button
        :class="{ 'is-active': editor.isActive('paragraph') }"
        @click="editor.chain().focus().setParagraph().run()"
      >
        <FaIcon name="paragraph" />
      </button>
      <hr class="vertical-line" />
      <button
        :class="{ 'is-active': editor.isActive('bold') }"
        @click="editor.chain().focus().toggleBold().run()"
      >
        <FaIcon name="bold" />
      </button>
      <button
        :class="{ 'is-active': editor.isActive('italic') }"
        @click="editor.chain().focus().toggleItalic().run()"
      >
        <FaIcon name="italic" />
      </button>
      <button
        :class="{ 'is-active': editor.isActive('strike') }"
        @click="editor.chain().focus().toggleStrike().run()"
      >
        <FaIcon name="strikethrough" />
      </button>
      <button
        :class="{ 'is-active': editor.isActive('underline') }"
        @click="editor.chain().focus().toggleUnderline().run()"
      >
        <FaIcon name="underline" />
      </button>
      <hr class="vertical-line" />
      <button
        :class="{ 'is-active': editor.isActive({ textAlign: 'left' }) }"
        @click="editor.chain().focus().setTextAlign('left').run()"
      >
        <FaIcon name="align-left" />
      </button>
      <button
        :class="{ 'is-active': editor.isActive({ textAlign: 'center' }) }"
        @click="editor.chain().focus().setTextAlign('center').run()"
      >
        <FaIcon name="align-center" />
      </button>
      <button
        :class="{ 'is-active': editor.isActive({ textAlign: 'right' }) }"
        @click="editor.chain().focus().setTextAlign('right').run()"
      >
        <FaIcon name="align-right" />
      </button>

      <button
        :class="{ 'is-active': editor.isActive({ textAlign: 'justify' }) }"
        @click="editor.chain().focus().setTextAlign('justify').run()"
      >
        <FaIcon name="align-justify" />
      </button>
      <hr class="vertical-line" />
      <button
        :class="{ 'is-active': editor.isActive('bulletList') }"
        @click="editor.chain().focus().toggleBulletList().run()"
      >
        <FaIcon name="list-ul" />
      </button>
      <button
        :class="{ 'is-active': editor.isActive('orderedList') }"
        @click="editor.chain().focus().toggleOrderedList().run()"
      >
        <FaIcon name="list-ol" />
      </button>
      <hr class="vertical-line" />
      <button @click="editor.chain().focus().setHorizontalRule().run()">
        <FaIcon name="horizontal-rule" />
      </button>
    </BubbleMenu>
    <EditorContent
      :data-pw="`tiptap_editor_${dataPw}`"
      :editor="editor"
    />
    <small
      v-if="editor && maxlength"
      class="opacity-60"
    >
      {{ editor.storage.characterCount.characters() }}/{{ maxlength }} caractères
    </small>
  </div>
</template>

<script lang="ts" setup>
import { type EditorModelValue, type EditorProps, useEditor } from '../composables/useEditor';
import { EditorContent } from '@tiptap/vue-3';
import { BubbleMenu } from '@tiptap/vue-3/menus';
import { onBeforeUnmount } from 'vue';

import { type NoStylingProps } from './TipTapEditor.component';

const emit = defineEmits<{
  (e: 'update:modelValue', value: EditorModelValue): void;
}>();

type Props = Omit<EditorProps, 'modelValue'> & {
  modelValue: EditorModelValue;
  dataPw?: string;
} & NoStylingProps;
const props = withDefaults(defineProps<Props>(), { noStyling: false, dataPw: undefined });

const { editor } = useEditor(props, emit);

onBeforeUnmount(() => {
  editor.value?.destroy();
});
</script>

<style lang="scss" scoped>
.vertical-line {
  width: 2px;
  height: 100%;
  background-color: black;
  border: none;
  margin: 0;
}

.bubble-menu {
  padding: calc(var(--padding) / 2);
  background-color: var(--color-secondary-container);
  color: var(--color-on-secondary-container);
  border-radius: calc(var(--border-radius) / 2);
  display: flex;
  flex-direction: row;
  gap: calc(var(--space) / 4);
  flex-wrap: nowrap;
  width: fit-content;

  hr {
    background-color: rgb(var(--color-on-secondary-container-rgb) / 0.1);
  }

  button {
    border: none;
    background: none;
    color: var(--color-on-secondary-container);
    width: 30px;
    height: 30px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: nowrap;

    &:hover {
      background-color: var(--color-secondary);
      color: var(--color-on-secondary);
    }

    &.is-active {
      background-color: var(--color-secondary);
      color: var(--color-on-secondary);
    }
  }
}
</style>

<style lang="scss">
.tiptap p.is-empty::before {
  color: rgb(var(--color-on-surface-container-highest-rgb) / 0.3);
  content: attr(data-placeholder);
  float: left;
  height: 0;
  pointer-events: none;
}
</style>
