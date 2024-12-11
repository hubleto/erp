import React, { Component } from 'react';
import Form, { FormProps, FormState } from 'adios/Form';
import InputTags2 from 'adios/Inputs/Tags2';
import FormInput from 'adios/FormInput';
import { getUrlParam } from 'adios/Helper';

export interface FormDocumentProps extends FormProps {
  creatingForModel?: string,
  creatingForId?: number,
}

export interface FormDocumentState extends FormState {}

export default class FormDocument<P, S> extends Form<FormDocumentProps,FormDocumentState> {
  static defaultProps: any = {
    ...Form.defaultProps,
    model: 'CeremonyCrmMod/Core/Documents/Models/Document',
  };

  props: FormDocumentProps;
  state: FormDocumentState;

  translationContext: string = 'mod.core.documents.formDocument';

  constructor(props: FormDocumentProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormDocumentProps) {
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

    if (R.COMPANY_DOCUMENT) {
      var companyEntryURL = window.ConfigEnv.rewriteBase+"customers/companies?recordId="+R.COMPANY_DOCUMENT.id_company;
      var companyEntryType = "Company"
    }
    if (R.LEAD_DOCUMENT) {
      var leadEntryURL = window.ConfigEnv.rewriteBase+"sales/leads?recordId="+R.LEAD_DOCUMENT.id_lead;
      var leadEntryType = "Lead"
    }
    if (R.DEAL_DOCUMENT) {
      var dealEntryURL = window.ConfigEnv.rewriteBase+"sales/deals?recordId="+R.DEAL_DOCUMENT.id_deal;
      var dealEntryType = "Deal"
    }

    return (
      <>
        <div className='card mt-4'>
          <div className='card-body'>
              {this.inputWrapper('name', {readonly: this.props.readonly})}
              {this.inputWrapper('file', {readonly: this.props.readonly})}
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
        </div>
      </>
    );
  }

}
