import React, { Component, createRef, RefObject } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormUserRoleProps extends HubletoFormProps {}
interface FormUserRoleState extends HubletoFormState {}

export default class FormUserRole<P, S> extends HubletoForm<FormUserRoleProps,FormUserRoleState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Auth/Models/UserRole',
  };

  props: FormUserRoleProps;
  state: FormUserRoleState;

  translationContext: string = 'Hubleto\\App\\Community\\Settings\\Loader';
  translationContextInner: string = 'Components\\FormUserRole';

  constructor(props: FormUserRoleProps) {
    super(props);
  }

  getStateFromProps(props: FormUserRoleProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('User role')}</small>
      <h2>{this.state.record.role ?? '-'}</h2>
    </>;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;

    let permissions: any = {};

    try {
      permissions = JSON.parse(R.permissions);
    } catch (ex) {
      permissions = {};
    }

    if (!permissions) permissions = {};

    return <>
      <div className='card'>
        <div className='card-body'>
          {this.inputWrapper("role")}
          {this.inputWrapper("description")}
          {this.inputWrapper("grant_all")}
        </div>
      </div>
      {this.divider('Permissions for records with designated owner, manager or team')}
      <div className='list'>
        <div className='list-item'><div className='flex gap-2 justify-between p-1'>
          <div>Reading</div>
          <div>
            <select
              onChange={(event) => {
                permissions.recordsRead = event.currentTarget.value;
                this.updateRecord({permissions: JSON.stringify(permissions)});
              }}
              value={permissions.recordsRead ?? 'owned'}
            >
              <option value='owned'>Can read only owned records</option>
              <option value='owned-and-managed'>Can read only owned and managed records</option>
              <option value='all'>Can read all records</option>
            </select>
          </div>
        </div></div>
        <div className='list-item'><div className='flex gap-2 justify-between p-1'>
          <div>Modifying</div>
          <div>
            <select
              onChange={(event) => {
                permissions.recordsModify = event.currentTarget.value;
                this.updateRecord({permissions: JSON.stringify(permissions)});
              }}
              value={permissions.recordsModify ?? 'owned'}
            >
              <option value='owned'>Can modify only owned records</option>
              <option value='owned-and-managed'>Can modify only owned and managed records</option>
              <option value='all'>Can modify all records</option>
            </select>
          </div>
        </div></div>
      </div>
      {R.grant_all || R.id <= 0 ? null :
        <Table
          uid='user_role_permissions'
          model='Hubleto/App/Community/Settings/Models/RolePermission'
          customEndpointParams={{idRole: this.state.id}}
        ></Table>
      }
    </>
  }
}
