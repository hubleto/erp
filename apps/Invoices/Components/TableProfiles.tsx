import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormProfile, { FormProfileProps } from './FormProfile';

interface TableProfilesProps extends TableExtendedProps {
  idCustomer?: number,
}

interface TableProfilesState extends TableExtendedState {
}

export default class TableProfiles extends TableExtended<TableProfilesProps, TableProfilesState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
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