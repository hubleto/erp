import React, { Component } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';

export interface FormMailProps extends HubletoFormProps {}
export interface FormMailState extends HubletoFormState {}

export default class FormMail<P, S> extends HubletoForm<FormMailProps,FormMailState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Mail/Models/Mail',
  };

  props: FormMailProps;
  state: FormMailState;

  translationContext: string = 'HubletoApp\\Community\\Mail\\Loader::Components\\FormMail';

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
      <small>Mail {this.state.record.is_template ? 'Template' : null}</small>
    </>;
  }

  sendMail() {
  }

  renderSaveButton(): null|JSX.Element {
    return <>
      <button onClick={() => this.saveRecord()} className="btn btn-add-outline">
        <span className="icon"><i className={"fa-solid fa-" + (this.state.record.is_template ? 'file-lines' : 'file-pen')}></i></span>
        <span className="text">{this.translate(this.state.record.is_template ? 'Save template' : 'Save draft')}</span>
      </button>
    </>;
  }

  renderHeaderLeft(): null|JSX.Element {
    return <>
      {super.renderHeaderLeft()}
      {this.state.record.is_template ? null :
        <button onClick={() => this.sendMail()} className="btn btn-add">
          <span className="icon"><i className="fas fa-paper-plane"></i></span>
          <span className="text">{this.translate('Send')}</span>
        </button>
      }
    </>;
  }

  renderContent(): JSX.Element {
    return <>
      <div className='flex gap-2'>
        <div className='flex-3'>
          {this.inputWrapper('to')}
          {this.inputWrapper('cc')}
          {this.inputWrapper('bcc')}
          {this.inputWrapper('subject')}
          {this.inputWrapper('body')}
        </div>
        <div className='flex-1'>
          {/* {this.inputWrapper('id_owner')} */}
          {this.inputWrapper('from')}
          {this.inputWrapper('priority')}
          {this.inputWrapper('datetime_created')}
          {this.inputWrapper('datetime_sent')}
          {this.inputWrapper('color')}
          {this.inputWrapper('is_draft')}
          {this.inputWrapper('is_template')}
        </div>
      </div>
    </>;
  }
}

