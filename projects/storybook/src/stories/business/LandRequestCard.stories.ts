import type { Meta, StoryObj } from '@storybook/vue3-vite';

import { Card } from '@lychen/vue-components-business/land-request/card';

const meta = {
  title: 'Business/Terrain (Land)/ Card - Demande',
  component: Card,
  argTypes: {},
  args: {},
} satisfies Meta<typeof Card>;

export default meta;
type Story = StoryObj<typeof meta>;

const DefaultData = {
  uuid: '019b705c-46db-7974-ad15-84b918ecbdcb',
  firstName: 'John',
  lastName: 'Doe',
  minimalSurfaceRequested: 50,
  description:
    'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam nec metus vel ante finibus facilisis.',
  avatarUrl: 'https://images.pexels.com/photos/35417978/pexels-photo-35417978.png',
};
export const Default: Story = {
  args: {
    ...DefaultData,
  },
};

export const Approved: Story = {
  name: 'Utilisateur vérifié',
  args: {
    ...DefaultData,
    isApproved: true,
  },
};
