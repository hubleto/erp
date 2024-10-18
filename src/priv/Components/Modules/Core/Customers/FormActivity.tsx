import React, { Component } from 'react';
import Form, { FormProps, FormState } from 'adios/Form';
import InputTags2 from 'adios/Inputs/Tags2';
import FormInput from 'adios/FormInput';
import { getUrlParam } from 'adios/Helper';

export interface FormActivityProps extends FormProps {}

export interface FormActivityState extends FormState {}

export default class FormActivity<P, S> extends Form<FormActivityProps,FormActivityState> {
  static defaultProps: any = {
    ...Form.defaultProps,
    model: 'CeremonyCrmApp/Modules/Core/Customers/Models/Activity',
  };

  props: FormActivityProps;
  state: FormActivityState;

  constructor(props: FormActivityProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormActivityProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  renderHeaderLeft(): JSX.Element {
    return <>
      {this.state.isInlineEditing ? this.renderSaveButton() : this.renderEditButton()}
    </>;
  }

  renderHeaderRight(): JSX.Element {
    return <>
      {this.state.isInlineEditing ? this.renderDeleteButton() : null}
      {this.props.showInModal ? this.renderCloseButton() : null}
    </>;
  }

  renderTitle(): JSX.Element {
    if (getUrlParam('recordId') == -1) {
      return(
        <>
          <h2>
            {'New Activity'}
          </h2>
        </>
      );
    } else {
      return (
        <>
          <h2>
            {this.state.record.subject
              ?
              <div className='flex flex-col justify-center'>
                <span>{this.state.record.subject}</span>
                <span className='text-xs text-gray-400 font-normal'>Activity</span>
              </div>
              :
              <div className='flex flex-col justify-center'>
                <span>'[Undefined Subject]'</span>
                <span className='text-xs text-gray-400 font-normal'>Activity</span>
              </div>
              }
          </h2>
        </>
      );
    }
  }

  onBeforeSaveRecord(record: any) {
    if (record.id == -1) {
      record.completed = 0;
      record.id_user = globalThis.app.idUser;
    }

    return record;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;

    const showAdditional = R.id > 0 ? true : false;

    return (
      <>
        <div className='card mt-4'>
          <div className='card-header'>Activity Information</div>
          <div className='card-body'>
            {this.inputWrapper('id_activity_type')}
            {this.inputWrapper('subject')}
            {this.inputWrapper('id_company')}
            {this.inputWrapper('date_start')}
            {this.inputWrapper('time_start')}
            {this.inputWrapper('date_end')}
            {this.inputWrapper('time_end')}
            {this.inputWrapper('all_day')}
            {showAdditional ? this.inputWrapper('completed') : null}
            {showAdditional ? this.inputWrapper('id_user') : null}
          </div>
        </div>
      </>
    );
  }
}
