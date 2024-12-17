import React, { Component } from 'react';
import Form, { FormProps, FormState } from 'adios/Form';
import InputTags2 from 'adios/Inputs/Tags2';
import FormInput from 'adios/FormInput';
import { getUrlParam } from 'adios/Helper';

export interface FormActivityProps extends FormProps {
  creatingForModel?: string,
  creatingForId?: number,
}

export interface FormActivityState extends FormState {}

export default class FormActivity<P, S> extends Form<FormActivityProps,FormActivityState> {
  static defaultProps: any = {
    ...Form.defaultProps,
    model: 'CeremonyCrmMod/Core/Customers/Models/Activity',
  };

  props: FormActivityProps;
  state: FormActivityState;

  translationContext: string = 'ceremonycrmmod.core.customers.formActivity';

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
    if (this.state.creatingRecord) {
      return <h2>{globalThis.app.translate('New activity in calendar')}</h2>;
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
    console.log(R, showAdditional);
    if (R.COMPANY_ACTIVITY) {
      var companyEntryURL = globalThis.app.config.rewriteBase+"customers/companies?recordId="+R.COMPANY_ACTIVITY.id_company;
      var companyEntryType = "Company"
    }
    if (R.LEAD_ACTIVITY) {
      var leadEntryURL = globalThis.app.config.rewriteBase+"sales/leads?recordId="+R.LEAD_ACTIVITY.id_lead;
      var leadEntryType = "Lead"
    }
    if (R.DEAL_ACTIVITY) {
      var dealEntryURL = globalThis.app.config.rewriteBase+"sales/deals?recordId="+R.DEAL_ACTIVITY.id_deal;
      var dealEntryType = "Deal"
    }

    return (
      <>
        {this.inputWrapper('id_activity_type')}
        {this.inputWrapper('subject')}
        {showAdditional ? this.inputWrapper('completed') : null}
        {(this.props.creatingForModel == "Lead" || this.props.creatingForModel == "Deal" || !this.props.creatingForModel) && showAdditional && companyEntryType ?
          <>
            <a href={companyEntryURL} className='btn btn-primary'>
              <span className='icon'><i className='fas fa-arrow-up-right-from-square'></i></span>
              <span className='text'>Go to linked {companyEntryType}</span>
            </a>
            <br></br>
          </>
        : null}
        {(this.props.creatingForModel == "Company" || !this.props.creatingForModel) && showAdditional && leadEntryType ?
          <>
            <a href={leadEntryURL} className='btn btn-primary mt-2'>
              <span className='icon'><i className='fas fa-arrow-up-right-from-square'></i></span>
              <span className='text'>Go to linked {leadEntryType}</span>
            </a>
            <br></br>
          </>
        : null}
        {(this.props.creatingForModel == "Company" || !this.props.creatingForModel) && showAdditional && dealEntryType ?
          <a href={dealEntryURL} className='btn btn-primary mt-2'>
            <span className='icon'><i className='fas fa-arrow-up-right-from-square'></i></span>
            <span className='text'>Go to linked {dealEntryType}</span>
          </a>
        : null}

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
