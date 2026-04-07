import React, { Component } from "react";
import { deepObjectMerge, getUrlParam } from "@hubleto/react-ui/core/Helper";
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import { FormProps, FormState } from "@hubleto/react-ui/core/Form";

interface FormAutomatProps extends FormExtendedProps {}

interface FormAutomatState extends FormExtendedState {
  tablesKey: number,
  newStepId: number,
}

export default class FormAutomat<P, S> extends FormExtended<FormAutomatProps, FormAutomatState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: "Hubleto/App/Community/Workflow/Models/Automat",
  };

  props: FormAutomatProps;
  state: FormAutomatState;

  translationContext: string = 'Hubleto\\App\\Community\\Workflow\\Loader\\Loader';
  translationContextInner: string = 'Components\\FormAutomat';

  getStateFromProps(props: FormAutomatProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
    }
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Workflow automat')}</small>
      <h2>{this.state.record.name ?? '-'}</h2>
    </>;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;

    return <div className="flex flex-col gap-2" >
      {this.inputWrapper("name")}
      {this.inputWrapper("execution_order")}
      {this.inputWrapper("description")}
      {this.inputWrapper("conditions")}
      {this.inputWrapper("actions")}
    </div>;
  }
}
