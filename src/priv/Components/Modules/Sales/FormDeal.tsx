import React, { Component } from 'react';
import { deepObjectMerge, getUrlParam } from 'adios/Helper';
import Form, { FormProps, FormState } from 'adios/Form';
import InputTags2 from 'adios/Inputs/Tags2';
import InputTable from 'adios/Inputs/Table';
import FormInput from 'adios/FormInput';
import request from 'adios/Request';
import TableDealServices from './TableDealServices';

interface FormDealProps extends FormProps {}

interface FormDealState extends FormState {}

export default class FormDeal<P, S> extends Form<FormDealProps,FormDealState> {
  static defaultProps: any = {
    ...Form.defaultProps,
    model: 'CeremonyCrmApp/Modules/Sales/Sales/Models/Deal',
  };

  props: FormDealProps;
  state: FormDealState;

  constructor(props: FormDealProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormDealProps) {
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
            {'New Deal'}
          </h2>
        </>
      );
    } else {
      return (
        <>
          <h2>
            {this.state.record.title
              ? this.state.record.title
              : '[Undefined Deal Name]'}
          </h2>
        </>
      );
    }
  }

  changeDealStatus(idStep: number, R: any) {
    if (idStep == R.id_pipeline_step) return;
    request.get(
      'sales/change-pipeline-step',
      {
        idStep: idStep,
        idPipeline: R.id_pipeline,
        idDeal: R.id
      },
      (data: any) => {
        if (data.status == "success") {
          R.id_pipeline_step = data.returnStep.id;
          R.HISTORY = data.dealHistory;
          this.setState({record: R});
        }
      }
    );
  }

  getDealSumPrice(recordServices: any) {
    var dealSumPrice = 0;
    recordServices.map((service, index) => {
      if (service.unit_price && service.amount) {
        var sum = service.unit_price * service.amount
        if (service.discount) {
          sum = sum - (sum * (service.discount / 100))
        }
        if (service.tax) {
          sum = sum - (sum * (service.tax / 100))
        }
        dealSumPrice += sum;
      }
    });
    return Number(dealSumPrice.toFixed(2));
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;
    if (R.HISTORY && R.HISTORY.length > 0) R.HISTORY = this.state.record.HISTORY.reverse();

    return (
      <>
        <div className='grid grid-cols-2 gap-1' style=
          {{gridTemplateAreas:`
            'info info'
            'status status'
            'services services'
            'history history'
          `}}>
            <div className='card mt-2' style={{gridArea: 'info'}}>
              <div className='card-header'>Deal Information</div>
              <div className='card-body flex flex-row gap-2'>
                <div className='grow'>
                  {this.inputWrapper('title')}
                  {this.inputWrapper('id_company')}
                  {this.inputWrapper('id_person')}
                  <div className='flex flex-row *:w-1/2'>
                    {this.inputWrapper('price', {
                      readonly: R.SERVICES && R.SERVICES.length > 0 ? true : false,
                    })}
                    {this.inputWrapper('id_currency')}
                  </div>
                  {/* {showAdditional ? this.inputWrapper('id_status') : null} */}
                  {showAdditional && R.id_lead != null ?
                    <div className='flex flex-row justify-between'>
                      {this.inputWrapper('id_lead')}
                      <a className='btn btn-primary self-center' href={`leads?recordId=${R.id_lead}`}>
                        <span className='icon'><i className='fas fa-eye'></i></span>
                      </a>
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
                      targetColumn='id_deal'
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
              <>
                <div className='card mt-2' style={{gridArea: 'status'}}>
                  <div className='card-header'>Deal Progress</div>
                  <div className='card-body'>
                    {this.inputWrapper('id_pipeline')}
                    <div className=' flex flex-row gap-2 justify-center'>

                      {R.PIPELINE != null && R.PIPELINE.PIPELINE_STEPS.length > 0 ?
                        R.PIPELINE.PIPELINE_STEPS.map((s, i) => {
                          var statusColor: string = null;
                          {s.id <= R.id_pipeline_step ? statusColor = "btn-primary" : statusColor = "btn-light"}
                          return (
                            <>
                              <button
                                style={{height: "50px"}}
                                onClick={()=>{this.changeDealStatus(s.id, R)}}
                                className={`flex px-3 justify-center btn ${statusColor}`}
                              >
                                <span className='text text-center self-center'>{s.name}</span>
                              </button>
                              {i+1 == R.PIPELINE.PIPELINE_STEPS.length ? null
                              : <span className='icon flex'><i className='fas fa-angles-right self-center'></i></span>
                              }
                            </>
                          )
                        })
                      : null}

                    </div>
                  </div>
                </div>
                {showAdditional ?
                  <div className='card mt-2' style={{gridArea: 'services'}}>
                    <div className='card-header'>Services</div>
                    <div className='card-body flex flex-col gap-2'>
                      <TableDealServices
                        uid={this.props.uid + "_table_lead_services"}
                        data={{ data: R.SERVICES }}
                        dealTotal={R.SERVICES && R.SERVICES.length > 0 ? "Total: " + R.price + " " + R.CURRENCY.code : null}
                        descriptionSource='props'
                        description={{
                          ui: {
                            showHeader: false,
                            showFooter: true
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
                            discount: { type: "float", title: "Discount (%)" },
                            tax: { type: "float", title: "Tax (%)" },
                            __sum: { type: "none", title: "Sum", cellRenderer: ( table: TableDealServices, data: any, options: any): JSX.Element => {
                              if (data.unit_price && data.amount) {
                                var sum = data.unit_price * data.amount
                                if (data.discount) {
                                  sum = sum - (sum * (data.discount / 100))
                                }
                                if (data.tax) {
                                  sum = sum - (sum * (data.tax / 100))
                                }
                                sum = Number(sum.toFixed(2));
                                return (<>
                                    <span>{sum} {R.CURRENCY.code}</span>
                                  </>
                                );
                              }
                            },
                          },
                          },
                        }}
                        isUsedAsInput={true}
                        isInlineEditing={this.state.isInlineEditing}
                        readonly={!this.state.isInlineEditing}
                        onRowClick={() => this.setState({isInlineEditing: true})}
                        onChange={(table: TableDealServices) => {
                          this.updateRecord({ SERVICES: table.state.data?.data });
                          R.price = this.getDealSumPrice(R.SERVICES);
                          this.setState({record: R});
                        }}
                        onDeleteSelectionChange={(table: TableDealServices) => {
                          this.updateRecord({ SERVICES: table.state.data?.data ?? [] });
                        }}
                      ></TableDealServices>
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
                <div className='card mt-2' style={{gridArea: 'history'}}>
                  <div className='card-header'>Deal History</div>
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
                      <p className='text-gray-400'>Deal has no history</p>
                    }
                  </div>
                </div>
              </>

            : null}
        </div>
      </>
    );
  }
}