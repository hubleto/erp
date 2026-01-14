import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormEventSpeakerProps extends FormExtendedProps { }
interface FormEventSpeakerState extends FormExtendedState { }

export default class FormEventSpeaker<P, S> extends FormExtended<FormEventSpeakerProps, FormEventSpeakerState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
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
