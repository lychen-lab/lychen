export default {
  property: {
    name: {
      label: 'Name',
      placeholder: 'Cultivator',
    },
    permissions: {
      label: 'Permissions',
    },
    land_members: {
      default: 'no member | {count} member | {count} members',
    },
  },
  action: {
    create: {
      label: 'Create role',
      success: {
        message: 'Role created',
      },
      error: {
        message: 'Error while creating role',
      },
      pending: {
        message: 'Creating role',
      },
    },
    update: {
      label: 'Update role',
      success: {
        message: 'Role updated',
      },
      error: {
        message: 'Error while updating role',
      },
      pending: {
        message: 'Updating role',
      },
    },
    delete: {
      label: 'Delete role',
      success: {
        message: 'Role deleted',
      },
      error: {
        message: 'Error while deleting role',
      },
      pending: {
        message: 'Deleting role',
      },
    },
  },
};
