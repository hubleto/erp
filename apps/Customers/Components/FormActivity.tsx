import React, { Component } from 'react';
import Form, { FormProps, FormState } from 'adios/Form';
import FormInput from 'adios/FormInput';
import Lookup from 'adios/Inputs/Lookup';

export interface FormActivityProps extends FormProps {
  idCompany: number,
}

export interface FormActivityState extends FormState {}

export default class FormActivity<P, S> extends Form<FormActivityProps,FormActivityState> {
  static defaultProps: any = {
    ...Form.defaultProps,
    model: 'CeremonyCrmMod/Customers/Models/CompanyActivity',
  };

  props: FormActivityProps;
  state: FormActivityState;

  translationContext: string = 'ceremonycrmmod.core.customers.formActivity';

  renderHeaderLeft(): JSX.Element {
    return this.state.isInlineEditing ? this.renderSaveButton() : this.renderEditButton();
  }

  renderHeaderRight(): JSX.Element {
    return <>
      {this.state.isInlineEditing ? this.renderDeleteButton() : null}
      {this.props.showInModal ? this.renderCloseButton() : null}
    </>;
  }

  renderTitle(): JSX.Element {
    if (this.state.creatingRecord) {
      return <h2>{globalThis.app.translate('New activity for company')}</h2>;
    } else {
      return (
        <>
          <h2>{this.state.record.subject ?? ''}</h2>
          <small>Activity</small>
        </>
      );
    }
  }

  onBeforeSaveRecord(record: any) {
    if (record.id == -1) {
      record.completed = 0;
    }

    return record;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;

    const showAdditional: boolean = R.id > 0 ? true : false;

    return (
      <>
        {this.inputWrapper('id_company')}
        <FormInput title={"Contact Person"}>
          <Lookup {...this.getDefaultInputProps()}
            model='CeremonyCrmMod/Customers/Models/Person'
            endpoint={`customers/get-company-contacts`}
            customEndpointParams={{id_company: R.id_company}}
            value={R.id_person}
            onChange={(value: any) => {
              this.updateRecord({ id_person: value })
              if (R.id_person == 0) {
                R.id_person = null;
                this.setState({record: R})
              }
            }}
          ></Lookup>
        </FormInput>
        {this.inputWrapper('subject')}
        {this.inputWrapper('id_activity_type')}
        {showAdditional ? this.inputWrapper('completed') : null}
        {this.inputWrapper('date_start')}
        {this.inputWrapper('time_start')}
        {this.inputWrapper('date_end')}
        {this.inputWrapper('time_end')}
        {this.inputWrapper('all_day')}
        {this.inputWrapper('id_user', {readonly: true, value: globalThis.app.idUser})}
      </>
    );
  }
}
