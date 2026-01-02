import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormSpeakerProps extends HubletoFormProps { }
interface FormSpeakerState extends HubletoFormState { }

export default class FormSpeaker<P, S> extends HubletoForm<FormSpeakerProps, FormSpeakerState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Events/Models/Team',
  }

  props: FormSpeakerProps;
  state: FormSpeakerState;

  translationContext: string = 'Hubleto\\App\\Community\\Events\\Loader';
  translationContextInner: string = 'Components\\FormSpeaker';

  constructor(props: FormSpeakerProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Speaker')}</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }


}
