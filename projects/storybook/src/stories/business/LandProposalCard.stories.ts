import type { Meta, StoryObj } from '@storybook/vue3-vite';

import { Card } from '@lychen/vue-components-business/land-proposal/card';

const meta = {
  title: 'Business/Land Proposal Card',
  component: Card,
  argTypes: {},
  args: {},
} satisfies Meta<typeof Card>;

export default meta;
type Story = StoryObj<typeof meta>;

export const Default: Story = {
  args: {
    id: '23547766',
  },
};
