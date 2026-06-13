import { type JSONContent } from '@tiptap/core';
import Bold from '@tiptap/extension-bold';
import BulletList from '@tiptap/extension-bullet-list';
import CharacterCount from '@tiptap/extension-character-count';
import Document from '@tiptap/extension-document';
import DropCursor from '@tiptap/extension-dropcursor';
import GapCursor from '@tiptap/extension-gapcursor';
import HardBreak from '@tiptap/extension-hard-break';
import Heading from '@tiptap/extension-heading';
import Highlight from '@tiptap/extension-highlight';
import HorizontalRule from '@tiptap/extension-horizontal-rule';
import Italic from '@tiptap/extension-italic';
import ListItem from '@tiptap/extension-list-item';
import OrderedList from '@tiptap/extension-ordered-list';
import Paragraph from '@tiptap/extension-paragraph';
import Strike from '@tiptap/extension-strike';
import Text from '@tiptap/extension-text';
import TextAlign from '@tiptap/extension-text-align';
import Underline from '@tiptap/extension-underline';
import { type AnyExtension, Editor, useEditor as useTipTapEditor } from '@tiptap/vue-3';
import { computed, type ComputedRef, type Ref, watch } from 'vue';

export type EditorProps = {
  placeholder?: string;
  oneLine?: boolean;
  maxlength?: number;
  modelValue: EditorModelValue;
};

export type EditorModelValue = JSONContent | null | undefined;
export type EditorEmit = {
  (e: 'update:modelValue', value: EditorModelValue): void;
};

type UseEditor = {
  editor: Ref<Editor | undefined>;
  defaultExtensions: ComputedRef<AnyExtension[]>;
};

export function useEditor(
  props: EditorProps,
  emit: EditorEmit,
  extensions?: AnyExtension[],
): UseEditor {
  const defaultExtensions = computed(() => {
    const exts: AnyExtension[] = [
      TextAlign.configure({
        types: ['heading', 'paragraph'],
      }),
      Document,
      Paragraph,
      Heading,
      HorizontalRule,
      Text,
      OrderedList,
      BulletList,
      ListItem,
      CharacterCount,
      Highlight,
      Italic,
      Bold,
      Strike,
      DropCursor,
      Underline,
      GapCursor,
      HardBreak,
    ];
    if (props.oneLine) {
      const CustomDocument = Document.extend({
        content: 'block',
      });
      exts.push(CustomDocument);
    }
    return exts;
  });

  function options() {
    return {
      extensions: extensions ? extensions : defaultExtensions.value,
      content: props.modelValue,
      onUpdate: () => {
        if (!editor.value) {
          return;
        }

        emit('update:modelValue', editor.value.getJSON());
      },
    };
  }

  const editor = useTipTapEditor(options());

  watch(
    () => props.modelValue,
    (value: EditorModelValue) => {
      if (!editor.value) {
        return;
      }
      const isSame = JSON.stringify(editor.value.getJSON()) === JSON.stringify(value);

      if (isSame || !value) {
        return;
      }

      editor.value.commands.setContent(value, { emitUpdate: false });
    },
  );

  return {
    defaultExtensions,
    editor,
  };
}
