import type { Meta, StoryObj } from '@storybook/vue3-vite';

import { Card } from '@lychen/vue-components-business/land-proposal/card';

const meta = {
  title: 'Business/Terrain (Land)/ Card - Proposition',
  component: Card,
  argTypes: {},
  args: {},
} satisfies Meta<typeof Card>;

export default meta;
type Story = StoryObj<typeof meta>;

const DefaultData = {
  uuid: '019b6e7f-ab0b-736b-a4f9-c5f9335aeda3',
  title: 'Yupola Garden',
  description: 'Yupola Garden is a garden in the heart of the city.',
  surface: 100,
  altitude: 456,
  city: 'Annecy',
  image:
    'https://images.pexels.com/photos/147640/pexels-photo-147640.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
  clickable: true,
};

export const Default: Story = {
  args: DefaultData,
};

const LongDescriptionData = {
  uuid: '019b6e7f-ab0b-736b-a4f9-c5f9335aeda3',
  title: 'Yupola Garden',
  description:
    'Yupola Garden is a garden in the heart of the city. Yupola Garden is a garden in the heart of the city. Yupola Garden is a garden in the heart of the city. Yupola Garden is a garden in the heart of the city. Yupola Garden is a garden in the heart of the city. Yupola Garden is a garden in the heart of the city. Yupola Garden is a garden in the heart of the city. Yupola Garden is a garden in the heart of the city. Yupola Garden is a garden in the heart of the city. Yupola Garden is a garden in the heart of the city.',
  surface: 100,
  altitude: 456,
  city: 'Annecy',
  image:
    'https://images.pexels.com/photos/147640/pexels-photo-147640.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
};

export const LongDescription: Story = {
  name: 'Avec une description (trop) longue',
  args: LongDescriptionData,
};

export const Favorite: Story = {
  name: 'Terrain mis en favori',
  args: { ...DefaultData, isFavorite: true },
};
