import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';

export interface FormMailProps extends FormExtendedProps {}
export interface FormMailState extends FormExtendedState {}

export default class FormMail<P, S> extends FormExtended<FormMailProps,FormMailState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Mail/Models/Mail',
  };

  props: FormMailProps;
  state: FormMailState;

  translationContext: string = 'Hubleto\\App\\Community\\Mail\\Loader';
  translationContextInner: string = 'Components\\FormMail';

  constructor(props: FormMailProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormMailProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  renderTitle(): null|JSX.Element {
    return <>
      <h2>{this.state.record.subject ? this.state.record.subject : ''}</h2>
      <small>{this.translate('Mail')}</small>
    </>;
  }

  sendMail() {
  }

  // renderSaveButton(): null|JSX.Element {
  //   return <>
  //     <button onClick={() => this.saveRecord()} className="btn btn-add-outline">
  //       <span className="icon"><i className="fa-solid fa-file-pen"></i></span>
  //       <span className="text">{this.translate('Save draft')}</span>
  //     </button>
  //   </>;
  // }

  renderHeaderLeft(): null|JSX.Element {
    const R = this.state.record;
    const isSent = (R.datetime_sent !== null);

    return <>
      {super.renderHeaderLeft()}
      {isSent ? null :
        <button onClick={() => this.sendMail()} className="btn btn-add-outline">
          <span className="icon"><i className="fas fa-paper-plane"></i></span>
          <span className="text">{this.translate('Send now')}</span>
        </button>
      }
    </>;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    const isSent = (R.datetime_sent !== null);
    const readonly = isSent;

    return <>
      <div className='flex gap-2'>
        <div className='flex-3'>
          {this.inputWrapper('from')}
          {this.inputWrapper('to')}
          {this.inputWrapper('cc')}
          {this.inputWrapper('bcc')}
          {this.inputWrapper('reply_to')}
          {this.inputWrapper('subject', {cssClass: 'text-2xl'})}
          {this.inputWrapper('body_html', {readonly: readonly})}
        </div>
        {/* <div className='flex-1'>
          {this.inputWrapper('from')}
          {this.inputWrapper('priority')}
          {this.inputWrapper('datetime_created')}
          {this.inputWrapper('datetime_sent')}
          {this.inputWrapper('color')}
          {this.inputWrapper('is_draft')}
        </div> */}
      </div>
    </>;
  }
}

