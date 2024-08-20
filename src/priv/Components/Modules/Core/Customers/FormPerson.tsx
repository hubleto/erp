import React, { Component } from "react";
import { deepObjectMerge } from "adios/Helper";
import Form, { FormProps, FormState } from "adios/Form";
import InputVarchar from "adios/Inputs/Varchar";
import InputTags2 from "adios/Inputs/Tags2";
import InputTable from "adios/Inputs/Table";
import FormInput from "adios/FormInput";
import { Column } from "primereact/column";

interface FormPersonProps extends FormProps {}

interface FormPersonState extends FormState {}

export default class FormPerson<P, S> extends Form<FormPersonProps,FormPersonState> {
  static defaultProps: any = {
    model: "CeremonyCrmApp/Modules/Core/Customers/Models/Person",
  };

  props: FormPersonProps;
  state: FormPersonState;

  constructor(props: FormPersonProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormPersonProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  normalizeRecord(record) {
    /* if (record.PERSONS) record.PERSONS.map((item: any, key: number) => {
      record.PERSONS[key].id_company = {_useMasterRecordId_: true};
    });
    if (record.BUSINESS_ACCOUNT) {
      record.BUSINESS_ACCOUNT.id_company = {_useMasterRecordId_: true};
    }; */

    return record;
  }

  renderTitle(): JSX.Element {
    return (
      <>
        <h2>
          {this.state.record.last_name
            ? this.state.record.first_name + " " + this.state.record.last_name
            : "[no-name]"}
        </h2>
      </>
    );
  }

  renderContent(): JSX.Element {
    const R = this.state.record;

    return (
      <>
        <div className="grid grid-cols-2 gap-1">
          <div>
            <div className="card mt-4">
              <div className="card-header">Personal Information</div>
              <div className="card-body">
                {this.inputWrapper("first_name")}
                {this.inputWrapper("last_name")}
                {this.inputWrapper("id_company")}
                {this.inputWrapper("is_primary")}
              </div>
            </div>

            <div className="card mt-4">
              <div className="card-header">Addresses</div>
              <div className="card-body">
                <InputTable
                  {...this.getDefaultInputProps()}
                  model="CeremonyCrmApp/Modules/Core/Customers/Models/Address"
                  value={R.ADDRESSES}
                  onChange={(value: any) => {
                    this.updateRecord({ ADDRESSES: value });
                  }}
                  columns={{
                    street: { type: "varchar", title: "Street" },
                    city: { type: "varchar", title: "City" },
                    postal_code: { type: "varchar", title: "Postal Code" },
                    country: { type: "varchar", title: "Country" },
                  }}
                ></InputTable>
                {this.state.isInlineEditing ? (
                  <a
                    role="button"
                    onClick={() => {
                      if (!R.ADDRESSES) R.ADDRESSES = [];
                      R.ADDRESSES.push({
                        id_person: { _useMasterRecordId_: true },
                      });
                      this.setState({ record: R });
                    }}
                  >
                    + Add address
                  </a>
                ) : null}
              </div>
            </div>
            <div className="card mt-4">
              <div className="card-header">Contacts</div>
              <div className="card-body">
                <InputTable
                  {...this.getDefaultInputProps()}
                  model="CeremonyCrmApp/Modules/Core/Customers/Models/Contact"
                  value={R.CONTACTS}
                  onChange={(value: any) => {
                    this.updateRecord({ PERSONS_CONTACTS: value });
                  }}
                  columns={{
                    type: {
                      type: "varchar",
                      title: "Contact type",
                      enumValues: {"email" : "Email", "number" : "Phone Number"},
                    },
                    value: { type: "varchar", title: "Value" },
                  }}
                ></InputTable>
                {this.state.isInlineEditing ? (
                  <a
                    role="button"
                    onClick={() => {
                      if (!R.CONTACTS) R.CONTACTS = [];
                      R.CONTACTS.push({
                        id_person: { _useMasterRecordId_: true },
                      });
                      this.setState({ record: R });
                    }}
                  >
                    + Add Contact
                  </a>
                ) : null}
              </div>
            </div>
          </div>

          <div>
            <div className="card">
              <div className="card-header">this.state.record</div>
              <div className="card-body">
                <pre
                  style={{
                    color: "blue",
                    width: "100%",
                    fontFamily: "Courier New",
                    fontSize: "10px",
                  }}
                >
                  {JSON.stringify(R, null, 2)}
                </pre>
              </div>
            </div>
          </div>
        </div>
      </>
    );
  }
}
