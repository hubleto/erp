import React, { Component } from 'react';
import { deepObjectMerge, getUrlParam } from 'adios/Helper';
import Form, { FormProps, FormState } from 'adios/Form';
import InputTags2 from 'adios/Inputs/Tags2';
import InputTable from 'adios/Inputs/Table';
import FormInput from 'adios/FormInput';
import request from 'adios/Request';
import InputVarchar from 'adios/Inputs/Varchar';
import TableLeadServices from './TableLeadServices';

interface FormLeadProps extends FormProps {}

interface FormLeadState extends FormState {}

export default class FormLead<P, S> extends Form<FormLeadProps,FormLeadState> {
  static defaultProps: any = {
    ...Form.defaultProps,
    model: 'CeremonyCrmApp/Modules/Sales/Sales/Models/Lead',
  };

  props: FormLeadProps;
  state: FormLeadState;

  constructor(props: FormLeadProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormLeadProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  /* normalizeRecord(record) {

    return record;
  } */

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
            {'New Lead'}
          </h2>
        </>
      );
    } else {
      return (
        <>
          <h2>
            {this.state.record.title
              ? this.state.record.title
              : '[Undefined Lead Name]'}
          </h2>
        </>
      );
    }
  }

  convertLead(recordId: number) {
    request.get(
      'sales/convert-lead',
      {recordId: recordId},
      (data: any) => {
        if (data.status == "success") {
          location.assign(`../sales/deals?recordId=${data.idDeal}&recordTitle=${data.title}`)
        }
      }
    );
  }

  convertDealWarning(recordId: number) {
    globalThis.app.showDialogDanger(
      <>Are you sure you want to convert this Lead to a Deal?</>,
      {
        headerClassName: "dialog-warning-header",
        header: "Convert to a Deal",
        footer: <>
          <button
            className="btn btn-yellow"
            onClick={() => {this.convertLead(recordId)}}
          >
            <span className="icon"><i className="fas fa-forward"></i></span>
            <span className="text">Yes, convert to a Deal</span>
          </button>
          <button
            className="btn btn-transparent"
            onClick={() => {
              globalThis.app.lastShownDialogRef.current.hide();
            }}
          >
            <span className="icon"><i className="fas fa-times"></i></span>
            <span className="text">No, do not convert to a Deal</span>
          </button>
        </>
      }
    );
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;

    return (
      <>
        <div className='grid grid-cols-2 gap-1' style=
          {{gridTemplateAreas:`
            'notification notification'
            'info info'
            'services services'
            'history history'
          `}}>
            <div className='card mt-2' style={{gridArea: 'info'}}>
              <div className='card-header'>Lead Information</div>
              <div className='card-body flex flex-row gap-2'>
                <div className='grow'>
                  {this.inputWrapper('title')}
                  {this.inputWrapper('id_company')}
                  {this.inputWrapper('id_person')}
                  <div className='flex flex-row *:w-1/2'>
                    {this.inputWrapper('price')}
                    {this.inputWrapper('id_currency')}
                  </div>
                  {showAdditional ? this.inputWrapper('id_status') : null}
                  {showAdditional ?
                    <div className='w-full mt-2'>
                      {R.DEAL != null ?
                      <a className='btn btn-primary' href={`../sales/deals?recordId=${R.DEAL.id}&recordTitle=${R.DEAL.title}`}>
                        <span className='icon'><i className='fas fa-eye'></i></span>
                        <span className='text'>Go to the Deal</span>
                      </a>
                      :
                      <a className='btn btn-primary cursor-pointer' onClick={() => this.convertDealWarning(R.id)}>
                        <span className='icon'><i className='fas fa-rotate-right'></i></span>
                        <span className='text'>Convert to a Deal</span>
                      </a>}
                    </div>
                  : null}
                </div>
                <div className='border-l border-gray-200'></div>
                <div className='grow'>
                  {this.inputWrapper('id_user')}
                  {this.inputWrapper('date_expected_close')}
                  {this.inputWrapper('source_channel')}
                  {this.inputWrapper('note')}
                  <FormInput title='Labels'>
                    <InputTags2 {...this.getDefaultInputProps()}
                      value={this.state.record.LABELS}
                      model='CeremonyCrmApp/Modules/Core/Settings/Models/Label'
                      targetColumn='id_lead'
                      sourceColumn='id_label'
                      colorColumn='color'
                      onChange={(value: any) => {
                        this.updateRecord({LABELS: value});
                      }}
                    ></InputTags2>
                  </FormInput>
                  {showAdditional ? this.inputWrapper('is_archived') : null}
                </div>
              </div>
            </div>
            {showAdditional ?
              <div className='card mt-2' style={{gridArea: 'services'}}>
                <div className='card-header'>Services</div>
                <div className='card-body flex flex-row gap-2'>
                  <TableLeadServices
                    uid={this.props.uid + "_table_lead_services"}
                    data={{ data: R.SERVICES }}
                    descriptionSource='props'
                    description={{
                      ui: {
                        showHeader: false,
                        showFooter: false
                      },
                      permissions: {
                        canCreate: true,
                        canUpdate: true,
                        canDelete: true,
                        canRead: true,
                      },
                      columns: {
                        id_service: { type: "lookup", title: "Service", model: "CeremonyCrmApp/Modules/Core/Services/Models/Service" },
                        unit_price: { type: "float", title: "Unit Price" },
                        amount: { type: "int", title: "Amount" },
                      },
                    }}
                    isUsedAsInput={true}
                    isInlineEditing={this.state.isInlineEditing}
                    readonly={!this.state.isInlineEditing}
                    onChange={(table: TableLeadServices) => {
                      this.updateRecord({ SERVICES: table.state.data?.data });
                    }}
                    onDeleteSelectionChange={(table: TableLeadServices) => {
                      this.updateRecord({ SERVICES: table.state.data?.data ?? [] });
                    }}
                  ></TableLeadServices>
                </div>
                  {this.state.isInlineEditing ? (
                    <a
                      role="button"
                      onClick={() => {
                        if (!R.SERVICES) R.SERVICES = [];
                        R.SERVICES.push({
                          id_lead: { _useMasterRecordId_: true },
                        });
                        this.setState({ record: R });
                      }}>
                      + Add service
                    </a>
                  ) : null}
              </div>
            : null}
            {showAdditional ?
              <div className='card mt-2' style={{gridArea: 'history'}}>
                <div className='card-header'>Lead History</div>
                <div className='card-body min-h-[100px] flex justify-center' style={{flexDirection: "column", gap: "4px"}}>
                  {R.HISTORY.length > 0 ?
                    R.HISTORY.map((history, key) => (
                      <div className='w-full flex flex-row justify-between'>
                        <div className='w-1/3'>
                            <p className='font-bold self-center text-sm text-left'>
                              {history.description}
                            </p>
                          </div>
                        <div className='w-1/3' style={{alignContent: "center"}}>
                          <hr style={{width: "100%", alignSelf: "center"}}/>
                        </div>
                        <div className='w-1/3 justify-center'>
                          <p className='self-center text-sm text-center'>
                            {history.change_date}
                          </p>
                        </div>
                      </div>
                    ))
                    :
                    <p className='text-gray-400'>Lead has no history</p>
                  }
                </div>
              </div>
            : null}
        </div>
      </>
    );
  }
}