import React, { Component, createRef } from 'react';
import { deepObjectMerge, getUrlParam } from 'adios/Helper';
import HubletoForm, {HubletoFormProps, HubletoFormState} from "../../../../src/core/Components/HubletoForm";
import InputTags2 from 'adios/Inputs/Tags2';
import FormInput from 'adios/FormInput';
import request from 'adios/Request';
import TableLeadProducts from './TableLeadProducts';
import { TabPanel, TabView } from 'primereact/tabview';
import Calendar from '../../Calendar/Components/Calendar';
import Lookup from 'adios/Inputs/Lookup';
import TableLeadDocuments from './TableLeadDocuments';
import ModalSimple from 'adios/ModalSimple';
import FormDocument, { FormDocumentProps, FormDocumentState } from '../../Documents/Components/FormDocument';
import FormActivity, { FormActivityProps, FormActivityState } from './FormActivity';
import Hyperlink from 'adios/Inputs/Hyperlink';

export interface FormLeadProps extends HubletoFormProps {
  newEntryId?: number,
}

export interface FormLeadState extends HubletoFormState {
  newEntryId?: number,
  showIdDocument: number,
  showIdActivity: number,
  activityCalendarTimeClicked: string,
  activityCalendarDateClicked: string,
  tableLeadProductsDescription: any,
  tableLeadDocumentsDescription: any,
}

export default class FormLead<P, S> extends HubletoForm<FormLeadProps,FormLeadState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Leads/Models/Lead',
  };

  props: FormLeadProps;
  state: FormLeadState;

  translationContext: string = 'HubletoApp\\Community\\Leads\\Loader::Components\\FormLead';

  constructor(props: FormLeadProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props),
      newEntryId: this.props.newEntryId ?? -1,
      showIdDocument: 0,
      showIdActivity: 0,
      activityCalendarTimeClicked: '',
      activityCalendarDateClicked: '',
      tableLeadProductsDescription: null,
      tableLeadDocumentsDescription: null,
    };
  }

  getStateFromProps(props: FormLeadProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  onAfterLoadFormDescription(description: any) {
    request.get(
      'api/table/describe',
      {
        model: 'HubletoApp/Community/Leads/Models/LeadProduct',
        idLead: this.state.id,
      },
      (description: any) => {
        this.setState({tableLeadProductsDescription: description} as any);
      }
    );
    request.get(
      'api/table/describe',
      {
        model: 'HubletoApp/Community/Leads/Models/LeadDocument',
        idLead: this.state.id,
      },
      (description: any) => {
        this.setState({tableLeadDocumentsDescription: description} as any);
      }
    );

    return description;
  }

  onAfterSaveRecord(saveResponse: any): void {
    let params = this.getEndpointParams() as any;
    let isArchived = saveResponse.savedRecord.is_archived;

    if (params.showArchive == false && isArchived == true) {
      this.props.onClose();
      this.props.parentTable.loadData();
    }
    else if (params.showArchive == true && isArchived == false) {
      this.props.onClose();
      this.props.parentTable.loadData();
    } else super.onAfterSaveRecord(saveResponse);
  }

  renderTitle(): JSX.Element {
    if (getUrlParam('recordId') == -1) {
      return <h2>{this.translate('New Lead')}</h2>;
    } else {
      return <>
        <h2>{this.state.record.title ? this.state.record.title : ''}</h2>
        <small>Lead</small>
      </>;
    }
  }

  renderSubTitle(): JSX.Element {
    return <small>{this.translate('Lead')}</small>;
  }

  getSumPrice(recordProducts: any) {
    var sumPrice = 0;

    recordProducts.map((product, index) => {
      if (product.unit_price && product.amount && product._toBeDeleted_ != true) {
        var sum = product.unit_price * product.amount;
        if (product.tax) sum = sum + (sum * (product.tax / 100));
        if (product.discount) sum = sum - (sum * (product.discount / 100));
        sumPrice += sum;
      }
    });
    return Number(sumPrice.toFixed(2));
  }

  convertLead(recordId: number) {
    request.get(
      'leads/convert-to-deal',
      {recordId: recordId},
      (data: any) => {
        if (data.status == "success") {
          location.assign(globalThis.main.config.rewriteBase + `deals?recordId=${data.idDeal}&recordTitle=${data.title}`)
        }
      }
    );
  }

  convertDealWarning(recordId: number) {
    globalThis.main.showDialogDanger(
      <>
        <div>
          Are you sure you want to convert this Lead to a Deal?<br/>
        </div>
        <div className="alert-warning mt-4">
          This lead will be moved to archive after conversion.<br/>
        </div>
      </>,
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
              globalThis.main.lastShownDialogRef.current.hide();
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
    const lookupElement = createRef();
    var lookupData;

    const getLookupData = () => {
      if (lookupElement.current) {
        lookupData = lookupElement.current.state.data;
      }
    }

    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;

    if (R.HISTORY && R.HISTORY.length > 0) {
      if (R.HISTORY.length > 1 && (R.HISTORY[0].id < R.HISTORY[R.HISTORY.length-1].id))
        R.HISTORY = this.state.record.HISTORY.reverse();
    }

    if (R.DEAL) R.DEAL.checkOwnership = false;

    if (R.id > 0 && globalThis.main.idUser != R.id_user && !this.state.recordChanged) {
      return (
        <>
          <div className='w-full h-full flex flex-col justify-center'>
            <span className='text-center'>This lead belongs to a different user</span>
          </div>
        </>
      )
    }

    return (
      <>
        {this.state.id > 0 ?
          <div className="h-0 w-full text-right">
            <div className="badge badge-secondary badge-large">
              Lead value:&nbsp;{globalThis.main.numberFormat(R.price, 2, ",", " ")} {R.CURRENCY.code}
            </div>
          </div>
        : null}
        <TabView>
          <TabPanel header={this.translate('Lead')}>
            {R.is_archived == 1 ?
              <div className='alert-warning mt-2 mb-1'>
                <span className='icon mr-2'><i className='fas fa-triangle-exclamation'></i></span>
                <span className='text'>This lead is archived.</span>
              </div>
            : null}
            <div className='gap-1'>
              <div className='flex gap-1 mt-2'>
                <div className='flex-2 card'>
                  <div className='card-body flex flex-row gap-2'>
                    <div className='grow'>
                      {this.inputWrapper('identifier', {readonly: R.is_archived})}
                      {this.inputWrapper('title', {readonly: R.is_archived})}
                      <FormInput title={"Customer"}>
                        <Lookup {...this.getInputProps('id-customer')}
                          model='HubletoApp/Community/Customers/Models/Customer'
                          endpoint={`customers/get-customer`}
                          readonly={R.is_archived}
                          value={R.id_customer}
                          onChange={(value: any) => {
                            this.updateRecord({ id_customer: value, id_person: null });
                            if (R.id_customer == 0) {
                              R.id_customer = null;
                              this.setState({record: R});
                            }
                          }}
                        ></Lookup>
                      </FormInput>
                      <FormInput title={"Contact Person"}>
                        <Lookup {...this.getInputProps('id-person')}
                          model='HubletoApp/Community/Customers/Models/Person'
                          customEndpointParams={{id_customer: R.id_customer}}
                          readonly={R.is_archived}
                          endpoint={`contacts/get-customer-contacts`}
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
                      <div className='flex flex-row *:w-1/2'>
                        {this.inputWrapper('price', {
                          readonly: (R.PRODUCTS && R.PRODUCTS.length) > 0 || R.is_archived ? true : false,
                        })}
                        {this.inputWrapper('id_currency')}
                      </div>
                      {showAdditional ? this.inputWrapper('id_lead_status', {readonly: R.is_archived}) : null}
                      {showAdditional ?
                        <div className='w-full mt-2'>
                          {R.DEAL != null ?
                          <a className='btn btn-primary' href={`${globalThis.main.config.url}/deals/${R.DEAL.id}`}>
                            <span className='icon'><i className='fas fa-arrow-up-right-from-square'></i></span>
                            <span className='text'>{this.translate('Go to deal')}</span>
                          </a>
                          :
                          <a className='btn btn-primary cursor-pointer' onClick={() => this.convertDealWarning(R.id)}>
                            <span className='icon'><i className='fas fa-rotate-right'></i></span>
                            <span className='text'>Convert to Deal</span>
                          </a>}
                        </div>
                      : null}
                    </div>
                    <div className='border-l border-gray-200'></div>
                    <div className='grow'>
                      {this.inputWrapper('id_user', {readonly: R.is_archived})}
                      {this.inputWrapper('date_expected_close', {readonly: R.is_archived})}
                      {this.inputWrapper('source_channel', {readonly: R.is_archived})}
                      <FormInput title='Tags'>
                        <InputTags2 {...this.getInputProps('tags_input')}
                          value={this.state.record.TAGS}
                          readonly={R.is_archived}
                          model='HubletoApp/Community/Leads/Models/Tag'
                          targetColumn='id_lead'
                          sourceColumn='id_tag'
                          colorColumn='color'
                          onChange={(value: any) => {
                            R.TAGS = value;
                            this.setState({record: R});
                          }}
                        ></InputTags2>
                      </FormInput>
                      {showAdditional ? this.inputWrapper('date_created') : null}
                      {showAdditional ? this.inputWrapper('is_archived') : null}
                    </div>
                  </div>
                </div>
                {this.state.id > 0 ?
                  <div className='flex-1 card'>
                    <div className="card-body">
                      <Calendar
                        onCreateCallback={() => this.loadRecord()}
                        readonly={R.is_archived}
                        eventsEndpoint={globalThis.main.config.rewriteBase + 'leads/get-calendar-events?idLead=' + R.id}
                        onDateClick={(date, time, info) => {
                          this.setState({
                            activityCalendarDateClicked: date,
                            activityCalendarTimeClicked: time,
                            showIdActivity: -1,
                          } as FormLeadState);
                        }}
                        onEventClick={(info) => {
                          this.setState({
                            showIdActivity: parseInt(info.event.id),
                          } as FormLeadState);
                          info.jsEvent.preventDefault();
                        }}
                        headerToolbar={{
                          left: 'prev,next',
                          center: 'title',
                          right: 'timeGridDay,timeGridWeek,dayGridMonth'
                        }}
                      ></Calendar>
                    </div>
                  </div>
                : null}
              </div>
              <div className='card card-body'>
                {this.inputWrapper('note', {readonly: R.is_archived})}
              </div>
              {showAdditional ?
                <div className='card'>
                  <div className='card-header'>Products</div>
                  <div className='card-body'>
                    {!R.is_archived ? (
                      <a
                        className="btn btn-add-outline mb-2"
                        onClick={() => {
                          if (!R.PRODUCTS) R.PRODUCTS = [];
                          R.PRODUCTS.push({
                            id: this.state.newEntryId,
                            id_lead: { _useMasterRecordId_: true },
                            amount: 1,
                          });
                          this.setState({ record: R, isInlineEditing: true, newEntryId: this.state.newEntryId - 1 } as FormLeadState);
                        }}
                      >
                        <span className="icon"><i className="fas fa-add"></i></span>
                        <span className="text">Add product</span>
                      </a>
                    ) : null}
                    <div className='w-full h-full overflow-x-auto'>
                      <TableLeadProducts
                        uid={this.props.uid + "_table_lead_products"}
                        data={{ data: R.PRODUCTS }}
                        leadTotal={R.PRODUCTS && R.PRODUCTS.length > 0 ? "Total: " + R.price + " " + R.CURRENCY.code : "Total: 0 " + R.CURRENCY.code }
                        descriptionSource='props'
                        customEndpointParams={{'idLead': R.id}}
                        description={{
                          permissions: this.state.tableLeadProductsDescription?.permissions,
                          ui: {
                            showHeader: false,
                            showFooter: true
                          },
                          columns: {
                            id_product: { type: "lookup", title: "Product", model: "HubletoApp/Community/Products/Models/Product",
                              cellRenderer: ( table: TableLeadProducts, data: any, options: any): JSX.Element => {
                                return (
                                  <FormInput>
                                    <Lookup {...this.getInputProps('lookup-product-1')}
                                      ref={lookupElement}
                                      model='HubletoApp/Community/Products/Models/Product'
                                      cssClass='min-w-44'
                                      value={data.id_product}
                                      onChange={(value: any) => {
                                        getLookupData();

                                        if (lookupData[value]) {
                                          data.id_product = value;
                                          data.unit_price = lookupData[value].unit_price;
                                          data.tax = lookupData[value].tax;
                                          this.updateRecord({ PRODUCTS: table.state.data?.data });
                                          this.updateRecord({ price: this.getSumPrice( R.PRODUCTS )});
                                        }
                                      }}
                                    ></Lookup>
                                  </FormInput>
                                )
                              },
                            },
                            unit_price: { type: "float", title: "Unit Price", readonly: true  },
                            amount: { type: "int", title: "Amount" },
                            discount: { type: "float", title: "Discount (%)" },
                            tax: { type: "float", title: "Tax (%)" },
                            __sum: { type: "none", title: "Sum after tax",
                              cellRenderer: ( table: TableLeadProducts, data: any, options: any): JSX.Element => {
                                if (data.unit_price && data.amount) {
                                  let sum = data.unit_price * data.amount;
                                  if (data.discount) sum = sum - (sum * (data.discount / 100));
                                  if (data.tax) sum = sum + (sum * (data.tax / 100));
                                  sum = Number(sum.toFixed(2));
                                  return (<><span>{sum + " " + R.CURRENCY.code}</span></>);
                                }
                              },
                            },
                          },
                          inputs: {
                            id_product: { type: "lookup", title: "Product", model: "HubletoApp/Community/Products/Models/Product",
                              cellRenderer: ( table: TableLeadProducts, data: any, options: any): JSX.Element => {
                                return (
                                  <FormInput>
                                    <Lookup {...this.getInputProps('lookup-product-2')}
                                      ref={lookupElement}
                                      model='HubletoApp/Community/Products/Models/Product'
                                      cssClass='min-w-44'
                                      value={data.id_product}
                                      onChange={(value: any) => {
                                        getLookupData();

                                        if (lookupData[value]) {
                                          data.id_product = value;
                                          data.unit_price = lookupData[value].unit_price;
                                          data.tax = lookupData[value].tax;
                                          this.updateRecord({ PRODUCTS: table.state.data?.data });
                                          this.updateRecord({ price: this.getSumPrice( R.PRODUCTS )});
                                        }
                                      }}
                                    ></Lookup>
                                  </FormInput>
                                )
                              },
                            },
                            unit_price: { type: "float", title: "Unit Price", readonly: true },
                            amount: { type: "int", title: "Amount" },
                            discount: { type: "float", title: "Discount (%)" },
                            tax: { type: "float", title: "Tax (%)" },
                            __sum: { type: "none", title: "Sum after tax" },
                          },
                        }}
                        isUsedAsInput={true}
                        isInlineEditing={this.state.isInlineEditing}
                        readonly={R.is_archived == true ? false : !this.state.isInlineEditing}
                        onRowClick={() => this.setState({isInlineEditing: true})}
                        onChange={(table: TableLeadProducts) => {
                          this.updateRecord({ PRODUCTS: table.state.data?.data });
                          R.price = this.getSumPrice(R.PRODUCTS);
                          this.setState({record: R});
                        }}
                        onDeleteSelectionChange={(table: TableLeadProducts) => {
                          this.updateRecord({ PRODUCTS: table.state.data?.data ?? [], price: this.getSumPrice(R.PRODUCTS)});
                        }}
                      ></TableLeadProducts>
                    </div>
                  </div>
                </div>
              : null}
            </div>
          </TabPanel>
          {showAdditional ?
            <TabPanel header={this.translate('Calendar')}>
              <Calendar
                onCreateCallback={() => this.loadRecord()}
                readonly={R.is_archived}
                views={"timeGridDay,timeGridWeek,dayGridMonth,listYear"}
                eventsEndpoint={globalThis.main.config.rewriteBase + 'leads/get-calendar-events?idLead=' + R.id}
                onDateClick={(date, time, info) => {
                  this.setState({
                    activityCalendarDateClicked: date,
                    activityCalendarTimeClicked: time,
                    showIdActivity: -1,
                  } as FormLeadState);
                }}
                onEventClick={(info) => {
                  this.setState({
                    showIdActivity: parseInt(info.event.id),
                  } as FormLeadState);
                  info.jsEvent.preventDefault();
                }}
              ></Calendar>
            </TabPanel>
          : null}
          {showAdditional ? (
            <TabPanel header={this.translate("Documents")}>
              <div className="divider"><div><div><div></div></div><div><span>{this.translate('Shared documents')}</span></div></div></div>
              {this.inputWrapper('shared_folder', {readonly: R.is_archived})}
              <div className="divider"><div><div><div></div></div><div><span>{this.translate('Local documents')}</span></div></div></div>
              {!R.is_archived ?
                <a
                  className="btn btn-add-outline mb-2"
                  onClick={() => this.setState({showIdDocument: -1} as FormLeadState)}
                >
                  <span className="icon"><i className="fas fa-add"></i></span>
                  <span className="text">Add document</span>
                </a>
              : null}
              <TableLeadDocuments
                uid={this.props.uid + "_table_lead_document"}
                data={{ data: R.DOCUMENTS }}
                descriptionSource="both"
                customEndpointParams={{idLead: R.id}}
                description={{
                  permissions: this.state.tableLeadDocumentsDescription?.permissions,
                  ui: {
                    showFooter: false,
                    showHeader: false,
                  },
                  columns: {
                    id_document: { type: "lookup", title: "Document", model: "HubletoApp/Community/Documents/Models/Document" },
                    hyperlink: { type: "varchar", title: "Link", cellRenderer: ( table: TableLeadDocuments, data: any, options: any): JSX.Element => {
                      return (
                        <FormInput>
                          <Hyperlink {...this.getInputProps('link_input')}
                            value={data.DOCUMENT.hyperlink}
                            readonly={true}
                          ></Hyperlink>
                        </FormInput>
                      )
                    }},
                  },
                  inputs: {
                    id_document: { type: "lookup", title: "Document", model: "HubletoApp/Community/Documents/Models/Document" },
                    hyperlink: { type: "varchar", title: "Link", readonly: true},
                  },
                }}
                isUsedAsInput={true}
                readonly={R.is_archived == true ? false : !this.state.isInlineEditing}
                onRowClick={(table: TableLeadDocuments, row: any) => {
                  this.setState({showIdDocument: row.id_document} as FormLeadState);
                }}
              />
              {this.state.showIdDocument != 0 ?
                <ModalSimple
                  uid='document_form'
                  isOpen={true}
                  type='right'
                >
                  <FormDocument
                    id={this.state.showIdDocument}
                    onClose={() => this.setState({showIdDocument: 0} as FormLeadState)}
                    showInModal={true}
                    descriptionSource="both"
                    description={{
                      defaultValues: {
                        creatingForModel: "HubletoApp/Community/Leads/Models/LeadDocument",
                        creatingForId: this.state.record.id,
                        origin_link: window.location.pathname + "?recordId=" + this.state.record.id,
                      }
                    }}
                    isInlineEditing={this.state.showIdDocument < 0 ? true : false}
                    showInModalSimple={true}
                    onSaveCallback={(form: FormDocument<FormDocumentProps, FormDocumentState>, saveResponse: any) => {
                      if (saveResponse.status = "success") {
                        this.loadRecord();
                        this.setState({ showIdDocument: 0 } as FormLeadState)
                      }
                    }}
                    onDeleteCallback={(form: FormDocument<FormDocumentProps, FormDocumentState>, saveResponse: any) => {
                      if (saveResponse.status = "success") {
                        this.loadRecord();
                        this.setState({ showIdDocument: 0 } as FormLeadState)
                      }
                    }}
                  />
                </ModalSimple>
              : null}
            </TabPanel>
          ) : null}
          {showAdditional ?
            <TabPanel header={this.translate('History')}>
              {R.HISTORY.length > 0 ?
                R.HISTORY.map((history, key) => (
                  <div key={key} className='w-full flex flex-row justify-between'>
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
            </TabPanel>
          : null}
        </TabView>
        {this.state.showIdActivity == 0 ? <></> :
          <ModalSimple
            uid='activity_form'
            isOpen={true}
            type='right'
          >
            <FormActivity
              id={this.state.showIdActivity}
              isInlineEditing={true}
              description={{
                defaultValues: {
                  id_lead: R.id,
                  date_start: this.state.activityCalendarDateClicked,
                  time_start: this.state.activityCalendarTimeClicked == "00:00:00" ? null : this.state.activityCalendarTimeClicked,
                  date_end: this.state.activityCalendarDateClicked,
                }
              }}
              idCustomer={R.id_customer}
              showInModal={true}
              showInModalSimple={true}
              onClose={() => { this.setState({ showIdActivity: 0 } as FormLeadState) }}
              onSaveCallback={(form: FormActivity<FormActivityProps, FormActivityState>, saveResponse: any) => {
                if (saveResponse.status == "success") {
                  this.setState({ showIdActivity: 0 } as FormLeadState);
                }
              }}
            ></FormActivity>
          </ModalSimple>
        }
      </>
    );
  }
}