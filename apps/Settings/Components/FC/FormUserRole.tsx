import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import Divider from '@hubleto/react-ui/components/fc/FormComponents/Divider';
import Table from '@hubleto/react-ui/components/fc/Table';

export interface FormUserRoleProps extends FormProps {}

const componentName = 'FormUserRole'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Settings';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormUserRoleProps) => {
  const form = React.useContext(FormMetaContext);
  const permissionsRaw: any = useRecordField('permissions');
  const grantAll: any = useRecordField('grant_all');

  let permissions: any = {};

  try {
    permissions = JSON.parse(permissionsRaw);
  } catch (ex) {
    permissions = {};
  }

  if (!permissions) permissions = {};

  return <>
    <div className='card'>
      <div className='card-body'>
        <Input fields='role' />
        <Input fields='description' />
        <Input fields='grant_all' />
      </div>
    </div>
    <Divider>{T.translate('Permissions for records with designated owner, manager or team')}</Divider>
    <div className='list'>
      <div className='list-item'><div className='flex gap-2 justify-between p-1'>
        <div>{T.translate('Reading')}</div>
        <div>
          <select
            onChange={(event) => {
              permissions.recordsRead = event.currentTarget.value;
              form.changeRecord({permissions: JSON.stringify(permissions)});
            }}
            value={permissions.recordsRead ?? 'owned'}
          >
            <option value='owned'>{T.translate('Can read only owned records')}</option>
            <option value='owned-and-managed'>{T.translate('Can read only owned and managed records')}</option>
            <option value='all'>{T.translate('Can read all records')}</option>
          </select>
        </div>
      </div></div>
      <div className='list-item'><div className='flex gap-2 justify-between p-1'>
        <div>{T.translate('Modifying')}</div>
        <div>
          <select
            onChange={(event) => {
              permissions.recordsModify = event.currentTarget.value;
              form.changeRecord({permissions: JSON.stringify(permissions)});
            }}
            value={permissions.recordsModify ?? 'owned'}
          >
            <option value='owned'>{T.translate('Can modify only owned records')}</option>
            <option value='owned-and-managed'>{T.translate('Can modify only owned and managed records')}</option>
            <option value='all'>{T.translate('Can modify all records')}</option>
          </select>
        </div>
      </div></div>
    </div>
    {grantAll || props.id <= 0 ? null :
      <Table
        uid='user_role_permissions'
        model='Hubleto/App/Community/Settings/Models/RolePermission'
        parentForm={form}
        endpointParams={{idRole: props.id}}
      ></Table>
    }
  </>;
}

/** FormUserRole */
const FormUserRole = (props: FormUserRoleProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/UserRole'}
    urlSlug='parent-app-slug/same-url-slug-as-in-table'
    endpointParams={{}}
    onAfterFormInitialized={(form: any) => {}}
    title={{field: 'role', sub: T.translate('User role')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormUserRole;
