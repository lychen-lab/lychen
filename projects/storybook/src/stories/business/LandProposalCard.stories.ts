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

const DefaultData = {
  uuid: '019b6e7f-ab0b-736b-a4f9-c5f9335aeda3',
  name: 'Yupola Garden',
  description: 'Yupola Garden is a garden in the heart of the city.',
  surface: 100,
  altitude: 456,
};

export const Default: Story = {
  args: DefaultData,
};
