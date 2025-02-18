import React, { Component } from 'react';
import HubletoForm, {HubletoFormProps, HubletoFormState} from "../../../../src/core/Components/HubletoForm";
import InputTags2 from 'adios/Inputs/Tags2';
import FormInput from 'adios/FormInput';
import { getUrlParam } from 'adios/Helper';

export interface FormDocumentProps extends HubletoFormProps {
  creatingForModel?: string,
  creatingForId?: number,
}

export interface FormDocumentState extends HubletoFormState {}

export default class FormDocument<P, S> extends HubletoForm<FormDocumentProps,FormDocumentState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Documents/Models/Document',
  };

  props: FormDocumentProps;
  state: FormDocumentState;

  translationContext: string = 'HubletoApp/Community/Documents/Components/FormDocument';

  constructor(props: FormDocumentProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormDocumentProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  renderTitle(): JSX.Element {
    if (getUrlParam('recordId') == -1) {
      return(
        <>
          <h2>
            {'New Document'}
          </h2>
        </>
      );
    } else {
      return (
        <>
          <h2>
            {this.state.record.name
              ?
              <div className='flex flex-col justify-center'>
                <span>{this.state.record.name}</span>
                <span className='text-xs text-gray-400 font-normal'>Document</span>
              </div>
              :
              <div className='flex flex-col justify-center'>
                <span>'[Undefined Name]'</span>
                <span className='text-xs text-gray-400 font-normal'>Document</span>
              </div>
              }
          </h2>
        </>
      );
    }
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional: boolean = R.id > 0 ? true : false;

    if (R.CUSTOMER_DOCUMENT) {
      var customerEntryURL = globalThis.main.config.rewriteBase+"customers/customers?recordId="+R.CUSTOMER_DOCUMENT.id_customer;
      var customerEntryType = "Customer"
    }
    if (R.LEAD_DOCUMENT) {
      var leadEntryURL = globalThis.main.config.rewriteBase+"leads?recordId="+R.LEAD_DOCUMENT.id_lead;
      var leadEntryType = "Lead"
    }
    if (R.DEAL_DOCUMENT) {
      var dealEntryURL = globalThis.main.config.rewriteBase+"deals?recordId="+R.DEAL_DOCUMENT.id_deal;
      var dealEntryType = "Deal"
    }

    return (
      <>
        <div className='card mt-4'>
          <div className='card-body'>
              {this.inputWrapper('name', {readonly: this.props.readonly})}
              {this.inputWrapper('file', {readonly: this.props.readonly})}
              {this.inputWrapper('hyperlink', {readonly: this.props.readonly})}
              {(this.props.creatingForModel == "Lead" || this.props.creatingForModel == "Deal" || !this.props.creatingForModel) && showAdditional && customerEntryType ?
                <>
                  <a href={customerEntryURL} className='btn btn-primary'>
                    <span className='icon'><i className='fas fa-arrow-up-right-from-square'></i></span>
                    <span className='text'>Go to linked {customerEntryType}</span>
                  </a>
                  <br></br>
                </>
              : null}
              {(this.props.creatingForModel == "Customer" || !this.props.creatingForModel) && showAdditional && leadEntryType ?
                <>
                  <a href={leadEntryURL} className='btn btn-primary mt-2'>
                    <span className='icon'><i className='fas fa-arrow-up-right-from-square'></i></span>
                    <span className='text'>Go to linked {leadEntryType}</span>
                  </a>
                  <br></br>
                </>
              : null}
              {(this.props.creatingForModel == "Customer" || !this.props.creatingForModel) && showAdditional && dealEntryType ?
                <a href={dealEntryURL} className='btn btn-primary mt-2'>
                  <span className='icon'><i className='fas fa-arrow-up-right-from-square'></i></span>
                  <span className='text'>Go to linked {dealEntryType}</span>
                </a>
              : null}
          </div>
        </div>
      </>
    );
  }

}
