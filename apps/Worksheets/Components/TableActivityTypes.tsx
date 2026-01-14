import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormActivityType from './FormActivityType';

interface TableActivityTypesProps extends TableExtendedProps {
  // Uncomment and modify these lines if you want to create URL-based filtering for your model
  // idCustomer?: number,
}

interface TableActivityTypesState extends TableExtendedState {
}

export default class TableActivityTypes extends TableExtended<TableActivityTypesProps, TableActivityTypesState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Worksheets/Models/ActivityType',
  }

  props: TableActivityTypesProps;
  state: TableActivityTypesState;

  translationContext: string = 'Hubleto\\App\\Community\\Worksheets\\Loader';
  translationContextInner: string = 'Components\\TableActivityTypes';

  constructor(props: TableActivityTypesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableActivityTypesProps) {
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
    return <FormActivityType {...formProps}/>;
  }
}