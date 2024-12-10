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
    model: 'CeremonyCrmApp/Modules/Core/Customers/Models/Activity',
  };

  props: FormActivityProps;
  state: FormActivityState;

  translationContext: string = 'mod.core.customers.formActivity';

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
    }

    return record;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;

    const showAdditional: boolean = R.id > 0 ? true : false;
    if (R.COMPANY_ACTIVITY) {
      var companyEntryURL = window.ConfigEnv.rewriteBase+"customers/companies?recordId="+R.COMPANY_ACTIVITY.id_company;
      var companyEntryType = "Company"
    }
    if (R.LEAD_ACTIVITY) {
      var leadEntryURL = window.ConfigEnv.rewriteBase+"sales/leads?recordId="+R.LEAD_ACTIVITY.id_lead;
      var leadEntryType = "Lead"
    }
    if (R.DEAL_ACTIVITY) {
      var dealEntryURL = window.ConfigEnv.rewriteBase+"sales/deals?recordId="+R.DEAL_ACTIVITY.id_deal;
      var dealEntryType = "Deal"
    }

    return (
      <>
        <div className='card mt-4'>
          <div className='card-header'>Activity Information</div>
          <div className='card-body flex flex-row gap-2'>
            <div className='grow'>
              {this.inputWrapper('id_activity_type')}
              {this.inputWrapper('subject')}
              {showAdditional ? this.inputWrapper('completed') : null}
              {this.inputWrapper('id_user', {readonly: true, value: globalThis.app.idUser})}
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
            </div>
            <div className='border-l border-gray-200'></div>
            <div className='grow'>
              {this.inputWrapper('date_start')}
              {this.inputWrapper('time_start')}
              {this.inputWrapper('date_end')}
              {this.inputWrapper('time_end')}
              {this.inputWrapper('all_day')}
            </div>
          </div>
        </div>
      </>
    );
  }
}
