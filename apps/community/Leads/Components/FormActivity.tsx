import React, { Component } from 'react';
import HubletoForm, {HubletoFormProps, HubletoFormState} from "../../../../src/core/Components/HubletoForm";
import FormInput from 'adios/FormInput';
import Lookup from 'adios/Inputs/Lookup';

export interface FormActivityProps extends HubletoFormProps {
  idLead: number,
  idCustomer?: number,
}

export interface FormActivityState extends HubletoFormState {
}

export default class FormActivity<P, S> extends HubletoForm<FormActivityProps,FormActivityState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Leads/Models/LeadActivity',
  };

  props: FormActivityProps;
  state: FormActivityState;

  translationContext: string = 'HubletoApp\\Community\\Leads\\Loader::Components\\FormActivity';

  constructor(props: FormActivityProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <h2>{this.state.record.subject ?? '-'}</h2>
      <small>Activity for a lead</small>
    </>;
  }

  onBeforeSaveRecord(record: any) {
    if (record.id == -1) {
      record.completed = 0;
    }

    return record;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
console.log(R, R.all_day);
    const showAdditional: boolean = R.id > 0 ? true : false;

    return <>
      {this.inputWrapper('id_lead')}
      <FormInput title={"Contact"}>
        <Lookup {...this.getInputProps()}
          model='HubletoApp/Community/Contacts/Models/Contact'
          endpoint={`contacts/get-customer-contacts`}
          customEndpointParams={{id_customer: this.props.idCustomer}}
          value={R.id_contact}
          onChange={(value: any) => {
            this.updateRecord({ id_contact: value })
            if (R.id_contact == 0) {
              R.id_contact = null;
              this.setState({record: R})
            }
          }}
        ></Lookup>
      </FormInput>
      {this.inputWrapper('subject', {cssClass: 'text-primary text-2xl'})}
      {this.inputWrapper('id_activity_type')}
      <div className='flex gap-2 w-full'>
        <div className='w-full'>
          {this.inputWrapper('all_day')}
        </div>
        <div className='w-full'>
          {this.inputWrapper('completed')}
        </div>
      </div>
      <div className='flex gap-2 w-full'>
        <div>
          {this.divider('Start')}
          {this.input('date_start')}
          {R.all_day ? null : this.input('time_start')}
        </div>
        <div>
          {this.divider('End')}
          {this.input('date_end')}
          {R.all_day ? null : this.input('time_end')}
        </div>
      </div>
      {this.inputWrapper('id_owner', {readonly: true, value: globalThis.main.idUser})}
    </>;
  }
}
