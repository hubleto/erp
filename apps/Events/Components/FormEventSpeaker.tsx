import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormEventSpeakerProps extends HubletoFormProps { }
interface FormEventSpeakerState extends HubletoFormState { }

export default class FormEventSpeaker<P, S> extends HubletoForm<FormEventSpeakerProps, FormEventSpeakerState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Events/Models/Team',
  }

  props: FormEventSpeakerProps;
  state: FormEventSpeakerState;

  translationContext: string = 'Hubleto\\App\\Community\\Events\\Loader';
  translationContextInner: string = 'Components\\FormEventSpeaker';

  constructor(props: FormEventSpeakerProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('EventSpeaker')}</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

}
