import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormPermission from './FormPermission';

interface TablePermissionsProps extends TableExtendedProps {
  idKey?: number,
}

interface TablePermissionsState extends TableExtendedState {
}

export default class TablePermissions extends TableExtended<TablePermissionsProps, TablePermissionsState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Api/Models/Permission',
  }

  props: TablePermissionsProps;
  state: TablePermissionsState;

  translationContext: string = 'Hubleto\\App\\Community\\Api\\Loader';
  translationContextInner: string = 'Components\\TablePermissions';

  constructor(props: TablePermissionsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TablePermissionsProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right wide';
    return params;
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/api/permissions/' + (id > 0 ? id : 'add'));
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      idKey: this.props.idKey
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    formProps.customEndpointParams.idKey = this.props.idKey
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_key: this.props.idKey };
    return <FormPermission {...formProps}/>;
  }
}