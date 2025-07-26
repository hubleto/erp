import React, { Component, createRef, RefObject } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormUserRoleProps extends HubletoFormProps {}
interface FormUserRoleState extends HubletoFormState {}

export default class FormUserRole<P, S> extends HubletoForm<FormUserRoleProps,FormUserRoleState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Settings/Models/UserRole',
  };

  props: FormUserRoleProps;
  state: FormUserRoleState;

  translationContext: string = 'HubletoApp\\Community\\Settings\\Loader::Components\\FormUserRole';

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
      <h2>{this.state.record.role ? this.state.record.role : '[Undefined Name]'}</h2>
    </>;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;

    return <>
      <div className='card'>
        <div className='card-body'>
          {this.inputWrapper("role")}
          {this.inputWrapper("description")}
          {this.inputWrapper("grant_all")}
        </div>
      </div>
      {R.grant_all || R.id <= 0 ? null :
        <Table
          uid='user_role_permissions'
          model='HubletoApp/Community/Settings/Models/RolePermission'
          customEndpointParams={{idRole: this.state.id}}
        ></Table>
      }
    </>
  }
}
