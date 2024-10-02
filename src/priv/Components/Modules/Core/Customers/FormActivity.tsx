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

  normalizeRecord(record) {
    if (record.TAGS) record.TAGS.map((item: any, key: number) => {
      record.TAGS[key].id_activity = {_useMasterRecordId_: true};
    });

    return record;
  }

  renderHeaderLeft(): JSX.Element {
    return <>
      {this.state.isInlineEditing ? this.renderSaveButton() : this.renderEditButton()}
    </>;
  }

  renderHeaderRight(): JSX.Element {
    return <>
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
              ? this.state.record.subject
              : '[Undefined Subject]'}
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
            {this.inputWrapper('subject')}
            {this.inputWrapper('id_company')}
            {this.inputWrapper('due_date')}
            {this.inputWrapper('due_time')}
            {this.inputWrapper('duration')}
            {showAdditional ? this.inputWrapper('completed') : null}
            {showAdditional ? this.inputWrapper('id_user') : null}

            {/* vypnuté kvôli chybe s company tags */}
            {/* <FormInput title='Categories'>
              <InputTags2
                {...this.getDefaultInputProps()}
                value={this.state.record.TAGS}
                model='CeremonyCrmApp/Modules/Core/Settings/Models/Tag'
                targetColumn='id_activity'
                sourceColumn='id_tag'
                colorColumn='color'
                onChange={(value: any) => {
                  this.updateRecord({ TAGS: value });
                }}
              ></InputTags2>
            </FormInput> */}
          </div>
        </div>
      </>
    );
  }
}
