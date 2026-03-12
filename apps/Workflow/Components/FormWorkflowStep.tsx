import React, { Component } from "react";
import { deepObjectMerge, getUrlParam } from "@hubleto/react-ui/core/Helper";
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import TableWorkflowSteps from "./TableWorkflowSteps";
import { FormProps, FormState } from "@hubleto/react-ui/core/Form";

interface FormWorkflowStepProps extends FormExtendedProps {}

interface FormWorkflowStepState extends FormExtendedState {
  tablesKey: number,
  newStepId: number,
}

export default class FormWorkflowStep<P, S> extends FormExtended<FormWorkflowStepProps, FormWorkflowStepState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: "Hubleto/App/Community/Workflow/Models/WorkflowStep",
  };

  props: FormWorkflowStepProps;
  state: FormWorkflowStepState;

  translationContext: string = 'Hubleto\\App\\Community\\Workflow\\Loader\\Loader';
  translationContextInner: string = 'Components\\FormWorkflowStep';

  constructor(props: FormWorkflowStepProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props),
      tablesKey: 0,
      newStepId: -1,
    };
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Workflow step')}</small>
      <h2>{this.state.record.name ?? '-'}</h2>
    </>;
  }

  componentDidUpdate(prevProps: FormProps, prevState: FormState): void {
    if (prevState.isInlineEditing != this.state.isInlineEditing) this.setState({tablesKey: Math.random()} as FormWorkflowStepState)
  }

  renderContent(): JSX.Element {
    const R = this.state.record;

    return <div>
      {this.inputWrapper('id_workflow')}
      {this.inputWrapper('name')}
      {this.inputWrapper('order')}
      {this.inputWrapper('color')}
      {this.inputWrapper('tag')}
      {this.inputWrapper('probability')}
    </div>;
  }
}
