import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormProfile, { FormProfileProps } from './FormProfile';

interface TableProfilesProps extends HubletoTableProps {
  idCustomer?: number,
}

interface TableProfilesState extends HubletoTableState {
}

export default class TableProfiles extends HubletoTable<TableProfilesProps, TableProfilesState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Invoices/Models/Profile',
  }

  props: TableProfilesProps;
  state: TableProfilesState;

  translationContext: string = 'Hubleto\\App\\Community\\Invoices\\Loader';
  translationContextInner: string = 'Components\\TableProfiles';

  constructor(props: TableProfilesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableProfilesProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right wide';
    return params;
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      idCustomer: this.props.idCustomer,
    }
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/invoices/profiles/' + (id > 0 ? id : 'add'));
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormProfileProps;
    return <FormProfile {...formProps}/>;
  }
}