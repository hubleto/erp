import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormProfile, { FormProfileProps } from './FormProfile';

interface TableProfilesProps extends HubletoTableProps {
  idCustomer?: number,
  showArchive?: boolean,
}

interface TableProfilesState extends HubletoTableState {
  showArchive: boolean,
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
      showArchive: props.showArchive ?? false,
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
      showArchive: this.props.showArchive ? 1 : 0,
      idCustomer: this.props.idCustomer,
    }
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.main.config.projectUrl + '/invoices/profiles/' + (id > 0 ? id : 'add'));
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormProfileProps;
    return <FormProfile {...formProps}/>;
  }
}