import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormSpeakerProps extends FormExtendedProps { }
interface FormSpeakerState extends FormExtendedState { }

export default class FormSpeaker<P, S> extends FormExtended<FormSpeakerProps, FormSpeakerState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
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
