import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormSpeaker from './FormSpeaker';

interface TableSpeakersProps extends TableExtendedProps {
  // Uncomment and modify these lines if you want to create URL-based filtering for your model
  // idCustomer?: number,
}

interface TableSpeakersState extends TableExtendedState {
}

export default class TableSpeakers extends TableExtended<TableSpeakersProps, TableSpeakersState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Events/Models/Speaker',
  }

  props: TableSpeakersProps;
  state: TableSpeakersState;

  translationContext: string = 'Hubleto\\App\\Community\\Events\\Loader';
  translationContextInner: string = 'Components\\TableSpeakers';

  constructor(props: TableSpeakersProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableSpeakersProps) {
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
      // Uncomment and modify these lines if you want to create URL-based filtering for your model
      // idCustomer: this.props.idCustomer,
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    // formProps.customEndpointParams.idCustomer = this.props.idCustomer;
    // if (!formProps.description) formProps.description = {};
    // formProps.description.defaultValues = { id_customer: this.props.idCustomer };
    return <FormSpeaker {...formProps}/>;
  }
}